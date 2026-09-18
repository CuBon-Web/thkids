<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAlbumafterTableForBeforeAfter extends Migration
{
    public function up()
    {
        Schema::table('albumafter', function (Blueprint $table) {
            if (!Schema::hasColumn('albumafter', 'before')) {
                $table->text('before')->nullable()->after('id');
            }
            if (!Schema::hasColumn('albumafter', 'after')) {
                $table->text('after')->nullable()->after('before');
            }
            if (!Schema::hasColumn('albumafter', 'title')) {
                $table->string('title')->default('')->after('after');
            }
            if (!Schema::hasColumn('albumafter', 'sort')) {
                $table->unsignedSmallInteger('sort')->default(0)->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('albumafter', function (Blueprint $table) {
            $table->dropColumn(['before', 'after', 'title', 'sort']);
        });
    }
}
