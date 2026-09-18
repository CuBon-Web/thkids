<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSortToProductCategoryTable extends Migration
{
    public function up()
    {
        Schema::table('product_category', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0)->after('status');
        });

        $ids = DB::table('product_category')->orderBy('id', 'asc')->pluck('id');
        foreach ($ids as $index => $id) {
            DB::table('product_category')->where('id', $id)->update(['sort' => $index + 1]);
        }
    }

    public function down()
    {
        Schema::table('product_category', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
