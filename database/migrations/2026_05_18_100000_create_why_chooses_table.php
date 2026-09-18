<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhyChoosesTable extends Migration
{
    public function up()
    {
        Schema::create('why_chooses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('why_chooses');
    }
}
