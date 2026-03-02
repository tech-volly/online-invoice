<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\Project;
use App\Models\InvoicePayment;
use App\Models\PaymentStatus;
use App\Models\Brand;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function invoice_payments(){
        return $this->hasMany(InvoicePayment::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function payment_status() {
        return $this->belongsTo(PaymentStatus::class);   
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($invoice) {
            $invoice->invoice_payments()->delete();
        });
    }
}
