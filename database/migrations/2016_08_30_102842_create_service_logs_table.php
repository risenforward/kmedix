<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id', false, true);
            $table->integer('user_id', false, true);
            $table->date('service_date');
            $table->string('part_number')->nullable();
            $table->tinyInteger('quantity', false, true)->nullable();
            $table->text('description');
            $table->integer('counter_1', false, true)->nullable();
            $table->integer('counter_2', false, true)->nullable();
            $table->integer('counter_3', false, true)->nullable();
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
        Schema::drop('service_log');
    }
}
