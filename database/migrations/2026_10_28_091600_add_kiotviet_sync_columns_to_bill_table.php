<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKiotvietSyncColumnsToBillTable extends Migration
{
    public function up()
    {
        Schema::table('bill', function (Blueprint $table) {
            if (!Schema::hasColumn('bill', 'kiotviet_order_id')) {
                $table->string('kiotviet_order_id', 50)->nullable()->index();
            }
            if (!Schema::hasColumn('bill', 'kiotviet_sync_status')) {
                $table->string('kiotviet_sync_status', 20)->nullable()->index();
            }
            if (!Schema::hasColumn('bill', 'kiotviet_synced_at')) {
                $table->timestamp('kiotviet_synced_at')->nullable();
            }
            if (!Schema::hasColumn('bill', 'kiotviet_sync_error')) {
                $table->text('kiotviet_sync_error')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('bill', function (Blueprint $table) {
            $columns = [];
            foreach (['kiotviet_order_id', 'kiotviet_sync_status', 'kiotviet_synced_at', 'kiotviet_sync_error'] as $column) {
                if (Schema::hasColumn('bill', $column)) {
                    $columns[] = $column;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
}
