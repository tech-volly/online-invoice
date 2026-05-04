<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ClientContact;
use App\Models\Country;
use App\Models\Currency;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_number', 'client_business_name', 'client_first_name', 'client_last_name', 'client_mobile', 'client_street_address_1', 'client_city', 
        'client_state', 'client_postalcode', 'client_country', 'add_shipping_address', 'shipping_street_address_1', 'shipping_city',
        'shipping_state', 'shipping_postalcode', 'shipping_country', 'client_invoicing_method', 'client_currency', 'client_email',
        'client_quotes_email', 'reminder_day', 'client_notes'
    ];

    public function contacts() {
        return $this->hasMany(ClientContact::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($client) {
            $client->contacts()->delete();
        });
    }


}
