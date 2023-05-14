<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('shop_id');
            $table->text('body_html')->nullable();
            $table->string('handle')->nullable();
            $table->string('published')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('published_scope')->nullable();
            $table->string('sort_order')->nullable();
            $table->string('template_suffix')->nullable();
            $table->string('title')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_collections');
    }
}
