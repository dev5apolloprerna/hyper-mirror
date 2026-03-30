<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_quotations', function (Blueprint $table) {
            $table->unsignedInteger('quotation_batch_id')->default(1)->after('iLeadId');
            $table->index(['iLeadId', 'quotation_batch_id'], 'lead_quotation_batch_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lead_quotations', function (Blueprint $table) {
            $table->dropIndex('lead_quotation_batch_idx');
            $table->dropColumn('quotation_batch_id');
        });
    }
};
