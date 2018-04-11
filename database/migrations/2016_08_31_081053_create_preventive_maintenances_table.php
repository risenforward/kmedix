<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreventiveMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id', false, true);
            $table->date('maintenance_date');
            $table->tinyInteger('extended', false, true);
            $table->tinyInteger('completed', false, true);
            $table->dateTime('completed_at')->nullable();
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
        Schema::drop('preventive_maintenances');
    }
}
