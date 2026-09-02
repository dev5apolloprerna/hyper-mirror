<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('gst_no', 15)->nullable()->after('company_name');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('packing_charges', 12, 2)->default(0)->after('delivery_charges');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('packing_charges');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('gst_no');
        });
    }
};
