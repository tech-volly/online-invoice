<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMultipleColumnsStructureToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('client_type')->nullable()->change();
            $table->string('client_street_address_1')->nullable()->change();
            $table->string('client_street_address_2')->nullable()->change();
            $table->string('client_city')->nullable()->change();
            $table->string('client_state')->nullable()->change();
            $table->string('client_postalcode')->nullable()->change();
            $table->string('client_country')->nullable()->change();
            $table->string('add_shipping_address')->nullable()->change();
            $table->string('client_invoicing_method')->nullable()->change();
            $table->string('client_currency')->nullable()->change();
            $table->string('client_notes')->nullable()->change();
            $table->text('lead_description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
}
