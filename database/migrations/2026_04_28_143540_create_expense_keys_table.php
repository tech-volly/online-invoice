<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->unsignedBigInteger('category_id');
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes(); // deleted_at

            // optional foreign key
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_keys');
    }
}
