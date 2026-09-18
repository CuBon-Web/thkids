<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagsToBlogsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('blogs') || Schema::hasColumn('blogs', 'tags')) {
            return;
        }

        Schema::table('blogs', function (Blueprint $table) {
            $table->text('tags')->nullable()->after('focus_keyword');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('blogs') || !Schema::hasColumn('blogs', 'tags')) {
            return;
        }

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
}

