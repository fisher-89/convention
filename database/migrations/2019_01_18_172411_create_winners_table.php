<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->char('openid',50)->comment('中奖用户微信openId')->index();
            $table->unsignedInteger('award_id')->comment('奖品ID')->index();
            $table->unsignedTinyInteger('is_receive')->comment('是否领奖 1是，0否')->default(1);
            $table->unsignedTinyInteger('round')->comment('抽奖第几轮')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('winners');
    }
}
