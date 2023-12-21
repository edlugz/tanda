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
        Schema::table('tanda_wallets', function (Blueprint $table) {
			$table->double('available_balance', 8, 2)->after('actual_balance')->default('0.00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_wallets', function (Blueprint $table) {
            $table->dropColumn('available_balance');
        });
    }
};
