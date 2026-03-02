<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_header_company_name')->nullable();
            $table->string('invoice_header_number')->nullable();
            $table->string('invoice_header_city')->nullable();
            $table->string('invoice_header_state')->nullable();
            $table->string('invoice_header_postalcode')->nullable();
            $table->string('invoice_header_tollfree')->nullable();
            $table->string('invoice_header_email')->nullable();
            $table->string('invoice_header_website')->nullable();
            $table->string('invoice_footer_company_name')->nullable();
            $table->string('invoice_footer_bsb_number')->nullable();
            $table->string('invoice_footer_acc_number')->nullable();
            $table->string('invoice_footer_email')->nullable();
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
        Schema::dropIfExists('invoice_settings');
    }
}
