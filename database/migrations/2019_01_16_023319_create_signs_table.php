<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signs', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->char('name',20)->comment('签到用户名');
            $table->char('mobile',11)->unique()->comment('签到手机');
            $table->char('openid',50)->comment('微信openId')->unique();
            $table->char('nickname',50)->comment('微信用户昵称')->default('');
            $table->string('avatar')->comment('微信头像')->default('');
            $table->unsignedTinyInteger('sex')->comment('微信用户的性别，值为1时是男性，值为2时是女性，值为0时是未知')->default(0);
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
        Schema::dropIfExists('signs');
    }
}
