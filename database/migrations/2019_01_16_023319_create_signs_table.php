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
            $table->char('number',50)->comment('邀请函编号')->index()->nullable()->default('');
            $table->char('hotel_name',50)->comment('酒店名称')->index()->nullable()->default('');
            $table->char('hotel_num',30)->comment('酒店房号')->nullable()->default('');
            $table->string('idcard')->comment('身份证')->nullable()->default('');
            $table->dateTime('start_time')->comment('入住开始时间')->nullable();
            $table->dateTime('end_time')->comment('入住结束时间')->nullable();
            $table->decimal('money',7,2)->comment('酒店费用')->nullable()->default('');
            $table->char('update_staff',10)->comment('修改人工号')->nullable()->default('');
            $table->char('update_name',10)->comment('修改人')->nullable()->default('');

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
