<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id', false, true);
            $table->tinyInteger('type', false, true);
            $table->dateTime('request_date');
            $table->string('description');
            $table->tinyInteger('status', false, true);
            $table->integer('attended_by', false, true)->nullable();
            $table->dateTime('attended_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('closed_at')->nullable();
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
        Schema::drop('service_requests');
    }
}
