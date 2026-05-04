<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientReminderAndInvoiceReminderFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'reminder_day')) {
                $table->integer('reminder_day')->default(15)->after('client_quotes_email');
            }
        });

        // Schema::table('invoices', function (Blueprint $table) {
        //     if (!Schema::hasColumn('invoices', 'invoice_sent_date')) {
        //         $table->dateTime('invoice_sent_date')->nullable()->after('invoice_emails');
        //     }
        //     if (!Schema::hasColumn('invoices', 'invoice_reminder_sent_at')) {
        //         $table->dateTime('invoice_reminder_sent_at')->nullable()->after('invoice_sent_date');
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'reminder_day')) {
                $table->dropColumn('reminder_day');
            }
        });

        // Schema::table('invoices', function (Blueprint $table) {
        //     if (Schema::hasColumn('invoices', 'invoice_reminder_sent_at')) {
        //         $table->dropColumn('invoice_reminder_sent_at');
        //     }
        //     if (Schema::hasColumn('invoices', 'invoice_sent_date')) {
        //         $table->dropColumn('invoice_sent_date');
        //     }
        // });
    }
}
