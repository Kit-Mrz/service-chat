<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateChatConversationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('chat_conversations', function (Blueprint $table){
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedBigInteger('user_id')->default(0)->comment('聊天用户Id');
            $table->unsignedBigInteger('deal_user_id')->default(0)->comment('处理者用户Id');
            $table->unsignedTinyInteger('is_deal')->default(0)->comment('处理状态(0=未处理;1=已处理)');
            $table->unsignedTinyInteger('is_end')->default(0)->comment('处理状态(0=未结束;1=已结束)');
            $table->unsignedTinyInteger('is_reply')->default(0)->comment('回复状态(0=未回复;1=已回复)');
            $table->unsignedTinyInteger('is_read')->default(0)->comment('已读状态(0=未读;1=已读)');
            $table->unsignedTinyInteger('customer_is_read')->default(0)->comment('客户已读状态(0=未读;1=已读)');
            $table->softDeletes();
            $table->timestamps();
            $table->index('user_id', 'idx_user_id');
            $table->index('deal_user_id', 'idx_deal_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('chat_conversations');
    }
}
