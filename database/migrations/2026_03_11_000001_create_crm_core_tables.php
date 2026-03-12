<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmCoreTables extends Migration
{
    public function up()
    {
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id('iShowroomId');
            $table->string('strShowRoomName', 50)->unique();
            $table->timestamps();
        });

        Schema::create('crm_roles', function (Blueprint $table) {
            $table->id('iRoleId');
            $table->string('strRole', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id('iCategoryId');
            $table->string('strCategoryName', 50)->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id('iProductId');
            $table->unsignedBigInteger('iCategoryId');
            $table->string('strProductName', 100);
            $table->integer('MRP')->default(0);
            $table->timestamps();

            $table->foreign('iCategoryId')->references('iCategoryId')->on('product_categories')->cascadeOnDelete();
        });

        Schema::create('user_showrooms', function (Blueprint $table) {
            $table->id('UserShowRoomId');
            $table->unsignedBigInteger('UserId');
            $table->unsignedBigInteger('ShowRoomId');
            $table->timestamps();

            $table->unique(['UserId', 'ShowRoomId']);
            $table->foreign('UserId')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ShowRoomId')->references('iShowroomId')->on('showrooms')->cascadeOnDelete();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id('iCustomerId');
            $table->string('strCustomer', 100);
            $table->string('strMobile', 10)->unique();
            $table->text('strAddress')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id('iLeadId');
            $table->unsignedBigInteger('iCustomerId');
            $table->string('iCurrentYearLeadId', 4);
            $table->string('strLeadNo')->unique();
            $table->tinyInteger('IsMeasureMentRequired')->default(0);
            $table->date('MeasurementVisitDate')->nullable();
            $table->text('SiteAddress')->nullable();
            $table->timestamp('CreatedDate')->useCurrent();
            $table->string('iCurrentLeadStatus', 50);
            $table->date('NetFollowupdate')->nullable();
            $table->tinyInteger('isFittingLeadOnly')->default(0);
            $table->tinyInteger('isFittingRequired')->default(0);
            $table->tinyInteger('isFittingChargeIncluded')->default(0);
            $table->integer('iFittingCharges')->default(0);
            $table->decimal('iLeadAmount', 16, 2)->default(0);
            $table->unsignedBigInteger('iQuotationId')->nullable();
            $table->unsignedBigInteger('iCreatedBy')->nullable();
            $table->unsignedBigInteger('iShowroomId')->nullable();
            $table->timestamps();

            $table->foreign('iCustomerId')->references('iCustomerId')->on('customers')->cascadeOnDelete();
            $table->foreign('iCreatedBy')->references('id')->on('users')->nullOnDelete();
            $table->foreign('iShowroomId')->references('iShowroomId')->on('showrooms')->nullOnDelete();
        });

        Schema::create('lead_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iLeadId');
            $table->text('strComments')->nullable();
            $table->date('NetFolloupwdate')->nullable();
            $table->string('iStatus', 50);
            $table->unsignedBigInteger('iEnterBy');
            $table->timestamp('EntryDate')->useCurrent();
            $table->timestamps();

            $table->foreign('iLeadId')->references('iLeadId')->on('leads')->cascadeOnDelete();
            $table->foreign('iEnterBy')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('lead_quotations', function (Blueprint $table) {
            $table->id('iQuotationId');
            $table->unsignedBigInteger('iLeadId');
            $table->unsignedBigInteger('iProductCategoryId');
            $table->unsignedBigInteger('iProductId');
            $table->decimal('decHeight', 16, 2);
            $table->decimal('decWidth', 16, 2);
            $table->decimal('decTotalSqft', 16, 2);
            $table->decimal('decRatePerSqft', 16, 2);
            $table->decimal('iAmount', 16, 2);
            $table->timestamps();

            $table->foreign('iLeadId')->references('iLeadId')->on('leads')->cascadeOnDelete();
            $table->foreign('iProductCategoryId')->references('iCategoryId')->on('product_categories')->cascadeOnDelete();
            $table->foreign('iProductId')->references('iProductId')->on('products')->cascadeOnDelete();
        });

        Schema::create('lead_designs', function (Blueprint $table) {
            $table->id('iLeadDesignId');
            $table->unsignedBigInteger('iLeadId');
            $table->string('strFilename', 255);
            $table->string('strTitle', 100)->nullable();
            $table->timestamps();

            $table->foreign('iLeadId')->references('iLeadId')->on('leads')->cascadeOnDelete();
        });

        Schema::create('lead_payments', function (Blueprint $table) {
            $table->id('iLeadPaymentId');
            $table->unsignedBigInteger('iLeadId');
            $table->decimal('iPaidAmount', 16, 2);
            $table->date('PaymentDate');
            $table->string('PaymentMode', 30);
            $table->unsignedBigInteger('iUserID')->nullable();
            $table->timestamps();

            $table->foreign('iLeadId')->references('iLeadId')->on('leads')->cascadeOnDelete();
            $table->foreign('iUserID')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_payments');
        Schema::dropIfExists('lead_designs');
        Schema::dropIfExists('lead_quotations');
        Schema::dropIfExists('lead_histories');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('user_showrooms');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('crm_roles');
        Schema::dropIfExists('showrooms');
    }
}
