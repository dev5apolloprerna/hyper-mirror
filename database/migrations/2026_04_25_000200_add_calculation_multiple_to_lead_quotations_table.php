<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_quotations', 'calculation_multiple')) {
                $table->unsignedTinyInteger('calculation_multiple')
                    ->default(3)
                    ->after('iProductId');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('lead_quotations', 'calculation_multiple')) {
                $table->dropColumn('calculation_multiple');
            }
        });
    }
};
