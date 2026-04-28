<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complain_master', function (Blueprint $table) {
            if (!Schema::hasColumn('complain_master', 'invoice_no')) {
                $table->string('invoice_no', 50)->nullable()->after('comment');
            }

            if (!Schema::hasColumn('complain_master', 'quotation_no')) {
                $table->string('quotation_no', 50)->nullable()->after('invoice_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complain_master', function (Blueprint $table) {
            if (Schema::hasColumn('complain_master', 'quotation_no')) {
                $table->dropColumn('quotation_no');
            }

            if (Schema::hasColumn('complain_master', 'invoice_no')) {
                $table->dropColumn('invoice_no');
            }
        });
    }
};
