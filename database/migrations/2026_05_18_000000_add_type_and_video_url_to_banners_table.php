<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeAndVideoUrlToBannersTable extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'type')) {
                $table->string('type', 20)->default('image')->after('image');
            }
            if (!Schema::hasColumn('banners', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('banners', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}
