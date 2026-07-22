<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id('iProductStockId');
            $table->unsignedBigInteger('iProductId');
            $table->unsignedBigInteger('iShowroomId');
            $table->integer('inside_quantity')->default(0);
            $table->integer('showroom_quantity')->default(0);
            $table->integer('minimum_quantity')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['iProductId', 'iShowroomId'], 'product_stocks_product_showroom_unique');
            $table->foreign('iProductId')->references('iProductId')->on('products')->cascadeOnDelete();
            $table->foreign('iShowroomId')->references('iShowroomId')->on('showrooms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
