<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('strUserName', 50)->nullable()->after('id');
            $table->string('strUserMobile', 15)->nullable()->unique()->after('mobile_number');
            $table->text('strUserAddress')->nullable()->after('strUserMobile');
            $table->unsignedBigInteger('iRoalId')->nullable()->after('role_id');

            $table->foreign('iRoalId')->references('iRoleId')->on('crm_roles')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['iRoalId']);
            $table->dropColumn(['strUserName', 'strUserMobile', 'strUserAddress', 'iRoalId']);
        });
    }
}
