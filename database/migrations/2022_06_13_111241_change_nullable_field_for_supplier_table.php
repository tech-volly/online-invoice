<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableFieldForSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_street_address_1')->nullable()->change();
            $table->string('supplier_city')->nullable()->change();
            $table->string('supplier_state')->nullable()->change();
            $table->string('supplier_postalcode')->nullable()->change();
            $table->dropColumn(['supplier_street_address_2', 'shipping_street_address_2']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
        });
    }
}
