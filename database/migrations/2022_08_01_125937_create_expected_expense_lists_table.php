<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ExpectedExpense;

class CreateExpectedExpenseListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expected_expense_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExpectedExpense::class);
            $table->string('expected_expense_name')->nullable();
            $table->float('expected_july_expense')->nullable();
            $table->float('expected_aug_expense')->nullable();
            $table->float('expected_sept_expense')->nullable();
            $table->float('expected_oct_expense')->nullable();
            $table->float('expected_nov_expense')->nullable();
            $table->float('expected_desc_expense')->nullable();
            $table->float('expected_jan_expense')->nullable();
            $table->float('expected_feb_expense')->nullable();
            $table->float('expected_mar_expense')->nullable();
            $table->float('expected_apr_expense')->nullable();
            $table->float('expected_may_expense')->nullable();
            $table->float('expected_june_expense')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expected_expense_lists');
    }
}
