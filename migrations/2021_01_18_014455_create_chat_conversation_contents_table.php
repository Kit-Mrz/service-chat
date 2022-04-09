<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateChatConversationContentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('chat_conversation_contents', function (Blueprint $table){
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedBigInteger('conversation_id')->default(0)->comment('会话Id');
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedTinyInteger('content_type')->default(0)->comment('会话类型(0=未知;1=文字;2=图片;3=视频;4=表情)');
            $table->text('content')->comment('聊天内容');
            $table->softDeletes();
            $table->timestamps();
            $table->index('conversation_id', 'idx_conversation_id');
            $table->index('user_id', 'idx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('chat_conversation_contents');
    }
}
