<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->unsignedBigInteger('created_by_user');
            $table->unsignedBigInteger('updated_by_user');
            $table->timestamp('checkout_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('updated_by_user')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('orders');
    }
}
