<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKiotvietCodeToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'kiotviet_code')) {
                $table->string('kiotviet_code', 100)->nullable()->after('sku')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'kiotviet_code')) {
                $table->dropColumn('kiotviet_code');
            }
        });
    }
}
