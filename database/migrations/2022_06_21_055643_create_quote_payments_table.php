<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Quote;
use App\Models\Product;

class CreateQuotePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Quote::class);
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
        Schema::dropIfExists('quote_payments');
    }
}
