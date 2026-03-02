<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\SubscriptionPayment;
use App\Models\PaymentStatus;
use App\Models\Brand;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function subscription_payments(){
        return $this->hasMany(SubscriptionPayment::class);
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
            $invoice->subscription_payments()->delete();
        });
    }
}
