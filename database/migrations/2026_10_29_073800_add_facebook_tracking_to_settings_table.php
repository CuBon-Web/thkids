<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFacebookTrackingToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'fb_pixel_id')) {
                $table->string('fb_pixel_id', 64)->nullable()->after('GA');
            }
            if (!Schema::hasColumn('settings', 'fb_capi_token')) {
                $table->text('fb_capi_token')->nullable()->after('fb_pixel_id');
            }
        });

        $pixelId = env('FACEBOOK_PIXEL_ID', '2225730044711426');
        $token = env('FACEBOOK_CAPI_ACCESS_TOKEN');

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('id', 1)->update([
                'fb_pixel_id' => $pixelId,
                'fb_capi_token' => $token ?: null,
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'fb_capi_token')) {
                $table->dropColumn('fb_capi_token');
            }
            if (Schema::hasColumn('settings', 'fb_pixel_id')) {
                $table->dropColumn('fb_pixel_id');
            }
        });
    }
}
