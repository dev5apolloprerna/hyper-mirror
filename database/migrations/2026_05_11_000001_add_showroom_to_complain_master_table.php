<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complain_master', function (Blueprint $table) {
            if (!Schema::hasColumn('complain_master', 'iShowroomId')) {
                $table->unsignedBigInteger('iShowroomId')->nullable()->after('invoice_no');
                $table->index('iShowroomId');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complain_master', function (Blueprint $table) {
            if (Schema::hasColumn('complain_master', 'iShowroomId')) {
                $table->dropIndex(['iShowroomId']);
                $table->dropColumn('iShowroomId');
            }
        });
    }
};
