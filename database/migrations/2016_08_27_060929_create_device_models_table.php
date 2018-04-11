<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_models', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id', false, true);
            $table->string('name');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->string('counter_1', 45)->nullable();
            $table->string('counter_2', 45)->nullable();
            $table->string('counter_3', 45)->nullable();
            $table->tinyInteger('active', false, true);
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
        Schema::drop('device_models');
    }
}
