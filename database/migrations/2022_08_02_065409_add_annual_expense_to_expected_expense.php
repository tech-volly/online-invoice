<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnnualExpenseToExpectedExpense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expected_expense_lists', function (Blueprint $table) {
            $table->float('expected_annual_expense')->nullable()->after('expected_june_expense');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expected_expense_lists', function (Blueprint $table) {
            //
        });
    }
}
