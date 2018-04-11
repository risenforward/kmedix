<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceRequestPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_request_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_request_id', false, true)->nullable();
            $table->string('photo');
            $table->tinyInteger('temp', false, true);
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
        Schema::drop('service_request_photos');
    }
}
