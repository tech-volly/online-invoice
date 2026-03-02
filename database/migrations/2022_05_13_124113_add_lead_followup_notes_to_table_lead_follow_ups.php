<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadFollowupNotesToTableLeadFollowUps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_follow_ups', function (Blueprint $table) {
            $table->string('followup_notes')->nullable()->after('followup_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_follow_ups', function (Blueprint $table) {
            //
        });
    }
}
