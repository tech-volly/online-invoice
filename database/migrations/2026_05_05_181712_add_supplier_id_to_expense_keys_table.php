<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierIdToExpenseKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')
                ->nullable()
                ->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_keys', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });
    }
}
