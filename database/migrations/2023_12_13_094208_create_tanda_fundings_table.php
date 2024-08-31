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
        Schema::create('tanda_fundings', function (Blueprint $table) {
            $table->id();
            $table->integer('fund_id');
            $table->string('fund_reference');
            $table->string('service_provider');
            $table->string('account_number');
            $table->string('amount');
            $table->string('response_status')->nullable();
            $table->string('response_message')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('request_status')->nullable();
            $table->string('request_message')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('timestamp')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_fundings');
    }
};
