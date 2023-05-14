<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('shop_id');
            $table->text('body_html')->nullable();
            $table->string('handle');
            $table->string('product_type')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('published_scope')->nullable();
            $table->string('status')->nullable();
            $table->text('tags')->nullable();
            $table->string('template_suffix')->nullable();
            $table->string('title')->nullable();
            $table->string('vendor')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('products');
    }
}
