<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ExpectedExpenseList;

class ExpectedExpense extends Model
{
    use HasFactory, SoftDeletes;

    public function epxected_expense_list(){
        return $this->hasMany(ExpectedExpenseList::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($expected_expense) {
            $expected_expense->epxected_expense_list()->delete();
        });
    }

}
