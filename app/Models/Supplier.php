<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SupplierAddress;
use App\Models\SupplierContact;
use App\Models\Country;
use App\Models\Currency;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_business_name', 'supplier_expense_category', 'supplier_first_name', 'supplier_last_name', 'supplier_mobile', 'supplier_street_address_1',
        'supplier_city', 'supplier_state', 'supplier_postalcode', 'supplier_country', 'add_shipping_address', 'shipping_street_address_1', 'shipping_city',
        'shipping_state', 'shipping_postalcode', 'shipping_country', 'supplier_currency', 'supplier_email', 'supplier_notes', 'is_status'
    ];

    public function contacts() {
        return $this->hasMany(SupplierContact::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($supplier) {
            $supplier->contacts()->delete();
        });
    }
}
