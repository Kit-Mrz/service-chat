<?php

namespace App\Model;

class ChatConversations extends Model
{
    /**
     * 处理状态(0=未处理;1=已处理)
     */
    const IS_DEAL_FALSE = 0;
    const IS_DEAL_TRUE  = 1;

    /**
     * 处理状态(0=未结束;1=已结束)
     */
    const IS_END_FALSE = 0;
    const IS_END_TRUE  = 1;

    /**
     * 回复状态(0=未回复;1=已回复)
     */
    const IS_REPLY_FALSE = 0;
    const IS_REPLY_TRUE  = 1;

    /**
     * 用户消息已读状态(0=未读;1=已读)
     */
    const IS_READ_FALSE = 0;
    const IS_READ_TRUE  = 1;

    /**
     * 会话状态(0=全部;1=已回复;2=未回复;3=未读;4=已接待;5=接待中;6=排队中)
     */
    const STATUS_ALL               = 0;
    const STATUS_ALREADY_REPLAY    = 1; // is_reply == 1
    const STATUS_UN_REPLAY         = 2; // is_reply == 0
    const STATUS_UN_READ           = 3; // is_read == 0
    const STATUS_ALREADY_RECEPTION = 4; // is_deal == 1 & is_end == 1
    const STATUS_RECEIVING         = 5; // is_deal == 1 && is_end == 0
    const STATUS_QUEUEING          = 6; // is_deal == 0

    /**
     * 会话状态(0=全部;1=已回复;2=未回复;3=未读;4=已接待;5接待中;6=排队中)
     *
     * @param null $status
     * @return array|mixed|null
     */
    public static function statusTranslate($status = null)
    {
        $map = [
            self::STATUS_ALL               => '全部',
            self::STATUS_ALREADY_REPLAY    => '已回复',
            self::STATUS_UN_REPLAY         => '未回复',
            self::STATUS_UN_READ           => '未读',
            self::STATUS_ALREADY_RECEPTION => '已接待',
            self::STATUS_RECEIVING         => '接待中',
            self::STATUS_QUEUEING          => '排队中',
        ];

        return $status === null ? $map : ($map[$status] ?? null);
    }

}
