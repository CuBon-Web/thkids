<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceFieldsToMessContactTable extends Migration
{
    public function up()
    {
        Schema::table('mess_contact', function (Blueprint $table) {
            if (!Schema::hasColumn('mess_contact', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('mess');
            }
            if (!Schema::hasColumn('mess_contact', 'service_name')) {
                $table->string('service_name')->nullable()->after('service_id');
            }
            if (!Schema::hasColumn('mess_contact', 'service_slug')) {
                $table->string('service_slug')->nullable()->after('service_name');
            }
            if (!Schema::hasColumn('mess_contact', 'service_cate_slug')) {
                $table->string('service_cate_slug')->nullable()->after('service_slug');
            }
        });
    }

    public function down()
    {
        Schema::table('mess_contact', function (Blueprint $table) {
            $table->dropColumn(['service_id', 'service_name', 'service_slug', 'service_cate_slug']);
        });
    }
}
