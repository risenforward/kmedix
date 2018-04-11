<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('logo')->nullable();
            $table->string('name');
            $table->string('phone_number', 45);
            $table->string('fax_number', 45)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('address');
            $table->string('web_address', 100);
            $table->tinyInteger('active', false, true)->default(1);
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
        Schema::drop('suppliers');
    }
}
