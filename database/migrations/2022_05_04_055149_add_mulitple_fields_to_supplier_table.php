<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMulitpleFieldsToSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('add_shipping_address')->nullable()->after('supplier_country');
            $table->string('shipping_street_address_1')->nullable()->after('add_shipping_address');
            $table->string('shipping_street_address_2')->nullable()->after('shipping_street_address_1');
            $table->string('shipping_city')->nullable()->after('shipping_street_address_2');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_postalcode')->nullable()->after('shipping_state');
            $table->string('shipping_country')->nullable()->after('shipping_postalcode');
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
