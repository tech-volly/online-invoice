<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Subscription;
use App\Models\Product;


class CreateSubscriptionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subscription::class);
            $table->foreignIdFor(Product::class);
            $table->text('product_description');
            $table->float('product_unit_price');
            $table->integer('product_quantity');
            $table->string('tax_selection');
            $table->float('product_subtotal');
            $table->float('product_gst');
            $table->float('product_grand_total');
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
        Schema::dropIfExists('subscription_payments');
    }
}
