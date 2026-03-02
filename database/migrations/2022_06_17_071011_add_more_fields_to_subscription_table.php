<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\PaymentStatus;
use App\Models\Brand;

class AddMoreFieldsToSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignIdFor(Client::class)->after('subscription_incremented_percentage');
            $table->foreignIdFor(Brand::class)->after('client_id');
            $table->foreignIdFor(PaymentStatus::class)->after('brand_id');
            $table->date('subscription_due_date')->after('payment_status_id');
            $table->integer('subscription_payment_terms')->after('subscription_due_date');
            $table->float('subscription_item_total')->after('subscription_payment_terms');
            $table->float('subscription_grand_gst')->after('subscription_item_total');
            $table->float('subscription_grand_total')->after('subscription_grand_gst');
            $table->string('subscription_method')->after('subscription_grand_total');
            $table->date('subscription_start_date')->after('subscription_cycle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
}
