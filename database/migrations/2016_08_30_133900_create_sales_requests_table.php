<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id', false, true);
            $table->date('request_date');
            $table->text('request_details');
            $table->text('notes')->nullable();
            $table->tinyInteger('status', false, true);
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
        Schema::drop('sales_requests');
    }
}
