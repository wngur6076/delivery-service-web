<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEateriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eateries', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('poster_image_path');
            $table->float('grade', 1, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('delivery_time');
            $table->integer('delivery_charge');
            $table->integer('minimum_order_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eateries');
    }
}
