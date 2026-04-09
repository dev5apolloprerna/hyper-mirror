<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('isDiscountApplicable')->default(0)->after('iFittingCharges');
            $table->decimal('decDiscountAmount', 16, 2)->default(0)->after('isDiscountApplicable');
            $table->tinyInteger('isGstApplicable')->default(0)->after('decDiscountAmount');
            $table->decimal('decGstAmount', 16, 2)->default(0)->after('isGstApplicable');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['isDiscountApplicable', 'decDiscountAmount', 'isGstApplicable', 'decGstAmount']);
        });
    }
};
