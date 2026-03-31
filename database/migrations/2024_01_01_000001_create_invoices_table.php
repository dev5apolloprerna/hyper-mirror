<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── invoices (header) ────────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('iInvoiceId');
            $table->string('strInvoiceNo', 30)->unique();
            $table->unsignedBigInteger('iShowroomId')->nullable();
            $table->unsignedBigInteger('iCreatedBy');
            $table->date('InvoiceDate');
            $table->text('strNotes')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();

            $table->foreign('iShowroomId')->references('iShowroomId')->on('showrooms')->onDelete('set null');
            $table->foreign('iCreatedBy')->references('id')->on('users')->onDelete('cascade');
        });

        // ── invoice_items (line items) ───────────────────────────────────
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id('iItemId');
            $table->unsignedBigInteger('iInvoiceId');
            $table->unsignedBigInteger('iCategoryId');
            $table->unsignedBigInteger('iProductId');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 16, 2)->default(0);
            $table->decimal('iAmount', 16, 2)->default(0);
            $table->timestamps();

            $table->foreign('iInvoiceId')->references('iInvoiceId')->on('invoices')->onDelete('cascade');
            $table->foreign('iCategoryId')->references('iCategoryId')->on('product_categories')->onDelete('cascade');
            $table->foreign('iProductId')->references('iProductId')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
