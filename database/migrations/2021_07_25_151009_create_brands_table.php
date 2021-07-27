<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('brand_description');
            $table->unsignedBigInteger('created_by_user');
            $table->unsignedBigInteger('updated_by_user');
            $table->timestamps();
        });

        /**
         * Adding foreign keys.
         * 
         * brands.created_by_user -> users.id
         * brands.updated_by_user -> users.id
         */
        Schema::table('brands', function (Blueprint $table) {
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
        Schema::dropIfExists('brands');
    }
}
