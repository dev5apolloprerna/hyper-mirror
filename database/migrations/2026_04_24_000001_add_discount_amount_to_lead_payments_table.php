<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_payments', function (Blueprint $table) {
            $table->decimal('iDiscountAmount', 16, 2)->default(0)->after('iPaidAmount');
        });
    }

    public function down(): void
    {
        Schema::table('lead_payments', function (Blueprint $table) {
            $table->dropColumn('iDiscountAmount');
        });
    }
};
