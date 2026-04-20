<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_cancel_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason', 150);
            $table->tinyInteger('iStatus')->default(1);
            $table->tinyInteger('isDelete')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_cancel_reasons');
    }
};
