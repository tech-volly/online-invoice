<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\QuotePayment;
use App\Models\Brand;
use App\Models\PaymentStatus;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function quote_payments(){
        return $this->hasMany(QuotePayment::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function payment_status() {
        return $this->belongsTo(PaymentStatus::class);   
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($quote) {
            $quote->quote_payments()->delete();
        });
    }
}
