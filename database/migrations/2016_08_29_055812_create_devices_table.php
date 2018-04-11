<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_model_id', false, true);
            $table->integer('customer_id', false, true)->nullable();
            $table->string('serial_number');
            $table->date('install_date')->nullable();
            $table->integer('installed_by', false, true)->nullable();
            $table->tinyInteger('warranty', false, true)->nullable();
            $table->tinyInteger('extended_warranty', false, true)->nullable();
            $table->date('extended_warranty_start')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('devices');
    }
}
