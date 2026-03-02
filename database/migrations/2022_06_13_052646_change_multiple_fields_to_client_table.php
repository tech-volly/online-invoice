<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMultipleFieldsToClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['client_type', 'client_fullname', 'client_telephone', 'client_street_address_2', 'shipping_street_address_2']);
            $table->string('client_street_address_1')->nullable()->change();
            $table->string('client_city')->nullable()->change();
            $table->string('client_state')->nullable()->change();
            $table->string('client_postalcode')->nullable()->change();
            $table->string('client_country')->default('Australia')->change();
            $table->string('client_currency')->default('AUD Australian Dollar')->change();
            $table->string('shipping_country')->default('Australia')->change();
            $table->text('client_notes')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
