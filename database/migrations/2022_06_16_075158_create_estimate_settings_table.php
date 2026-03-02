<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_header_company_name')->nullable();
            $table->string('estimate_header_number')->nullable();
            $table->string('estimate_header_city')->nullable();
            $table->string('estimate_header_state')->nullable();
            $table->string('estimate_header_postalcode')->nullable();
            $table->string('estimate_header_tollfree')->nullable();
            $table->string('estimate_header_email')->nullable();
            $table->string('estimate_header_website')->nullable();
            $table->string('estimate_footer_company_name')->nullable();
            $table->string('estimate_footer_bsb_number')->nullable();
            $table->string('estimate_footer_acc_number')->nullable();
            $table->string('estimate_footer_email')->nullable();
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
        Schema::dropIfExists('estimate_settings');
    }
}
