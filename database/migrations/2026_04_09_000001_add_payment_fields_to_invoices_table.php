<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_mode', ['cash', 'bank'])
                ->default('cash')
                ->after('status');
            $table->boolean('payment_received')
                ->default(false)
                ->after('payment_mode');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'payment_received']);
        });
    }
};
