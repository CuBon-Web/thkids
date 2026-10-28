<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKiotvietCodeToVariantSkuValueTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('variant_sku_value', 'kiotviet_code')) {
            Schema::table('variant_sku_value', function (Blueprint $table) {
                $table->string('kiotviet_code', 100)->nullable()->after('sku')->index();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('variant_sku_value', 'kiotviet_code')) {
            Schema::table('variant_sku_value', function (Blueprint $table) {
                $table->dropColumn('kiotviet_code');
            });
        }
    }
}
