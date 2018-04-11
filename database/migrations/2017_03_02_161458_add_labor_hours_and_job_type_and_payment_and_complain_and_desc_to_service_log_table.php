<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLaborHoursAndJobTypeAndPaymentAndComplainAndDescToServiceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_log', function (Blueprint $table) {
            $table->string('labor_hours');
            $table->string('job_type');
            $table->string('payment');
            $table->string('complain');
            $table->string('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_log', function (Blueprint $table) {
            $table->dropColumn('labor_hours');
            $table->dropColumn('job_type');
            $table->dropColumn('payment');
            $table->dropColumn('complain');
            $table->dropColumn('desc');
        });
    }
}
