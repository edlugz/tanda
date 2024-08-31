<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tanda_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_id');
            $table->string('payment_reference');
            $table->string('service_provider');
            $table->string('merchant_wallet')->nullable();
            $table->string('amount');
            $table->string('account_number')->nullable();
            $table->string('contact')->nullable();
            $table->string('service_provider_id')->nullable;
            $table->string('response_status')->nullable();
            $table->string('response_message')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('request_status')->nullable();
            $table->string('request_message')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('transaction_receipt')->nullable();
            $table->string('timestamp')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_transactions');
    }
};
