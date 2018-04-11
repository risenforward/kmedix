<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsumableWarrantyAndServiceLevelFieldsToDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->tinyInteger('consumable_warranty', false, true)->nullable()->after('warranty');
            $table->tinyInteger('contract_level', false, true)->nullable()->after('consumable_warranty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('consumable_warranty');
            $table->dropColumn('contract_level');
        });
    }
}
