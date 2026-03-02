<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\Brand;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number');
            $table->date('quote_date');
            $table->float('quote_item_total');
            $table->float('quote_grand_gst');
            $table->float('quote_grand_total');
            $table->string('quote_method');
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(Brand::class);
            $table->boolean('is_status')->default(1)->comment('1:active, 0:inactive');
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
        Schema::dropIfExists('quotes');
    }
}
