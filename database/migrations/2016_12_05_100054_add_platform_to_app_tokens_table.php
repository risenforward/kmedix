<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlatformToAppTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('app_tokens')->truncate();
        Schema::table('app_tokens', function (Blueprint $table) {
            $table->tinyInteger('platform', false, true)->after('app_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_tokens', function (Blueprint $table) {
            $table->removeColumn('platform');
        });
    }
}
