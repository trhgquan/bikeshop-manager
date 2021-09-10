<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bikes', function (Blueprint $table) {
            // Create the table structures.
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('bike_name');
            $table->string('bike_description');
            $table->unsignedBigInteger('bike_stock')->default(0);
            $table->unsignedBigInteger('bike_buy_price')->default(0);
            $table->unsignedBigInteger('bike_sell_price')->default(0);
            $table->unsignedBigInteger('created_by_user');
            $table->unsignedBigInteger('updated_by_user');
            $table->timestamps();
            $table->softDeletes();

            // Create foreign keys.
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->foreign('created_by_user')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by_user')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bikes');
    }
}
