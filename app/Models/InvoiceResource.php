<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InvoiceResourceImage;

class InvoiceResource extends Model
{
    use HasFactory, SoftDeletes;

    public function invoice_resource_images() {
        return $this->hasMany(InvoiceResourceImage::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($client) {
            $client->invoice_resource_images()->delete();
        });
    }
}
