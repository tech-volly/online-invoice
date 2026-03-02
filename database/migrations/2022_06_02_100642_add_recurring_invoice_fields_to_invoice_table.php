<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurringInvoiceFieldsToInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_invoice_recurring')->nullable()->after('brand_id');
            $table->string('invoice_subscription_name')->nullable()->after('is_invoice_recurring');
            $table->string('invoice_subscription_cycle')->nullable()->after('invoice_subscription_name');
            $table->date('invoice_subscription_date')->nullable()->after('invoice_subscription_cycle');
            $table->boolean('is_invoice_rec_increment')->nullable()->after('invoice_subscription_date');
            $table->float('invoice_incremented_percentage')->nullable()->after('is_invoice_rec_increment');
            $table->integer('invoice_parent_id')->nullable()->after('invoice_incremented_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
