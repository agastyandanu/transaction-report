<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('merchant_id');
            $table->integer('outlet_id');
            $table->datetime('transaction_time');
            $table->string('staff');
            $table->bigInteger('pay_amount');
            $table->string('payment_type');
            $table->string('customer_name');
            $table->bigInteger('tax');
            $table->bigInteger('change_amount');
            $table->bigInteger('total_amount');
            $table->string('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
