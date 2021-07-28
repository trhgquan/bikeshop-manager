<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            // Table structures.
            $table->unsignedBigInteger('bike_id')->unique();
            $table->bigInteger('stock')->default(0);
            $table->bigInteger('buy_price')->default(0);
            $table->bigInteger('sell_price')->default(0);
            $table->softDeletes();

            // Table foreign keys
            $table->foreign('bike_id')
                ->references('id')
                ->on('bikes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
