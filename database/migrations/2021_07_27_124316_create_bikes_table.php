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
        // Create the table.
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('bike_name');
            $table->string('bike_description');
            $table->unsignedBigInteger('created_by_user');
            $table->unsignedBigInteger('updated_by_user');
            $table->timestamps();
        });

        // Create foreign keys.
        Schema::table('bikes', function (Blueprint $table) {
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');

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
