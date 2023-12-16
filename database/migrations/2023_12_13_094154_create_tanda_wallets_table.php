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
        Schema::create('tanda_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
			$table->string('name');
			$table->string('wallet_account_number')->nullable();
			$table->string('ipnUrl');
			$table->string('username');
			$table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_wallets');
    }
};
