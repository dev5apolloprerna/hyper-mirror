<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockManagementTables extends Migration
{
    public function up()
    {
        // Current on-hand quantity of a product at a showroom.
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id('iStockId');
            $table->unsignedBigInteger('iProductId');
            $table->unsignedBigInteger('iShowroomId');
            $table->integer('iQuantity')->default(0);
            $table->timestamps();

            $table->unique(['iProductId', 'iShowroomId']);

            $table->foreign('iProductId')->references('iProductId')->on('products')->cascadeOnDelete();
            $table->foreign('iShowroomId')->references('iShowroomId')->on('showrooms')->cascadeOnDelete();
        });

        // Full audit trail / ledger of every stock change.
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('iMovementId');
            $table->unsignedBigInteger('iProductId');
            $table->unsignedBigInteger('iShowroomId');       // showroom this row's quantity affects
            $table->unsignedBigInteger('iRelatedShowroomId')->nullable(); // other side of a transfer
            $table->string('strType', 20);                   // in | out | transfer_in | transfer_out | invoice_out | invoice_reversal
            $table->integer('iQuantity');                     // always positive; strType decides the sign
            $table->integer('iBalanceAfter')->default(0);
            $table->string('strReason', 255)->nullable();
            $table->unsignedBigInteger('iReferenceId')->nullable(); // e.g. iInvoiceId
            $table->string('strReferenceType', 50)->nullable();     // e.g. 'invoice'
            $table->unsignedBigInteger('iCreatedBy')->nullable();
            $table->timestamps();

            $table->foreign('iProductId')->references('iProductId')->on('products')->cascadeOnDelete();
            $table->foreign('iShowroomId')->references('iShowroomId')->on('showrooms')->cascadeOnDelete();
            $table->foreign('iRelatedShowroomId')->references('iShowroomId')->on('showrooms')->nullOnDelete();
            $table->foreign('iCreatedBy')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_stocks');
    }
}
