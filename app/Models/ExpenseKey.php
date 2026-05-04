<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'category_id'
    ];

    public $timestamps = false; // since we manually added created_at only

    // Relationship (optional)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
