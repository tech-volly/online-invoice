<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypeForLeadFollowupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_follow_ups', function (Blueprint $table) {
            $table->text('followup_notes')->change();
            $table->date('lead_discussion_date')->nullable()->after('lead_id');
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
