<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_item_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_item_id');
            $table->unsignedBigInteger('option_id');
            $table->timestamps();

            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_item_options');
    }
}
