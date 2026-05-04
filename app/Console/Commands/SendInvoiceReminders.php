<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\PaymentStatus;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoice:send-reminders';
    protected $description = 'Send invoice reminder emails based on client reminder_day and invoice sent date.';

    public function handle()
    {
        $paidStatusId = PaymentStatus::where('name', 'Paid')->value('id');
        $cancelledStatusId = PaymentStatus::where('name', 'Cancelled')->value('id');

        $query = Invoice::with(['client', 'payment_status'])
            ->where('is_status', 1)
            ->where(function ($query) use ($paidStatusId, $cancelledStatusId) {
                $query->where('payment_status_id', '!=', $paidStatusId);
                if ($cancelledStatusId) {
                    $query->where('payment_status_id', '!=', $cancelledStatusId);
                }
            });

        Log::info('Invoice reminder query: ' . $query->toSql());
        Log::info('Query bindings: ' . json_encode($query->getBindings()));

        $invoices = $query->get();

        Log::info('Found ' . $invoices->count() . ' invoices to check for reminders.');
        foreach ($invoices as $invoice) {
            
            if (!$invoice->client) {
                Log::warning('Invoice ' . $invoice->invoice_number . ' has no client, skipping.');
                continue;
            }

            $reminderDays = $invoice->client->reminder_day ?? 15;
            $sentDate = Carbon::parse($invoice->invoice_due_date);
            $sendAfter = $sentDate->copy()->addDays($reminderDays);

            Log::info('Calculated send after date: ' . $sendAfter->toDateTimeString());
            Log::info('Current date: ' . Carbon::now()->toDateTimeString());
            Log::info('Invoice reminder sent at: ' . $sendAfter->lte(Carbon::now()) ? 'Yes' : 'No');
            if ($sendAfter->lte(Carbon::now())) {
                Log::info('Sending reminder for invoice ' . $invoice->invoice_number);
                $toEmails = [];
                if ($invoice->invoice_emails) {
                    foreach (explode(',', $invoice->invoice_emails) as $email) {
                        $email = trim($email);
                        Log::info('Checking invoice email: ' . $email);
                        if ($email) {
                            $toEmails[] = $email;
                        }
                    }
                }
                if (empty($toEmails) && $invoice->client->client_email) {
                    $toEmails[] = $invoice->client->client_email;
                }

                if (empty($toEmails)) {
                    continue;
                }

                $sent = sendInvoiceReminderEmail($invoice);
                if ($sent) {
                    $this->info('Reminder sent for invoice ' . $invoice->invoice_number);
                } else {
                    $this->error('Failed reminder for invoice ' . $invoice->invoice_number);
                }
            }
        }

        return 0;
    }
}
