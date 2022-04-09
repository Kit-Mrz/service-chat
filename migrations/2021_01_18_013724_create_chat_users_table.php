<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateChatUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('chat_users', function (Blueprint $table){
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedTinyInteger('platform')->default(0)->comment('平台类型(0=未知;1=伊蜜心选小程序;2=伊的家小程序)');
            $table->unsignedBigInteger('platform_user_id')->default(0)->comment('平台对应的用户Id');
            $table->string('user_type', 32)->default('')->comment('用户类型(staff=员工;customer=用户)');
            $table->unsignedTinyInteger('user_role')->default(0)->comment('用户角色(0=未知;1=客服)');
            $table->unsignedTinyInteger('status')->default(1)->comment('用户状态(0=禁用;1=启用)');

            $table->string('nickname', 64)->default('')->comment('用户昵称');
            $table->string('avatar', 512)->default('')->comment('用户头像');
            $table->char('mobile', 11)->default('')->comment('手机号');
            $table->string('describe', 64)->default('')->comment('头衔');
            $table->string('real_name', 64)->default('')->comment('用户名称');
            $table->timestamp('login_at')->nullable()->comment('上一次登录时间');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['platform', 'platform_user_id'], 'idx_platform_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('chat_users');
    }
}
