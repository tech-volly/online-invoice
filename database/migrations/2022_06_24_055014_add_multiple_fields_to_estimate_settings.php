<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleFieldsToEstimateSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimate_settings', function (Blueprint $table) {
            $table->string('estimate_header_street_address')->nullable()->after('estimate_header_number');
            $table->text('estimate_footer_notes')->nullable()->after('estimate_footer_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimate_settings', function (Blueprint $table) {
            //
        });
    }
}
