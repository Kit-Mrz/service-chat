<?php

namespace App\Model;


class ChatUsers extends Model
{
    /**
     * 平台类型
     * (0=未知; 1=伊蜜心选; 2=伊的家商城)
     */
    const PLATFORM_COMMUNITY = 1;
    const PLATFORM_MALL      = 2;

    // unknown=未知; staff=员工ID; customer=客户ID; wx=微信用户ID
    const USER_TYPE_STAFF    = 'staff';
    const USER_TYPE_CUSTOMER = 'customer';

    /**
     * 用户角色
     * 0=未知; 1=客服; 2=专业组; 3=业务导师;
     */
    const USER_ROLE_CUSTOMER_SERVICE = 1;
    const USER_ROLE_MAJOR            = 2;
    const USER_ROLE_TEACHER          = 3;

    // 是否商城(伊的家小程序)
    public static function isMall(int $platform) : bool
    {
        return $platform === self::PLATFORM_MALL;
    }

    // 是否社区(伊蜜心选小程序)
    public function isCommunity(int $platform) : bool
    {
        return $platform === self::PLATFORM_COMMUNITY;
    }

    // 是否员工
    public static function isStaff(string $userType) : bool
    {
        return $userType === self::USER_TYPE_STAFF;
    }

    // 是否客户
    public static function isCustomer(string $userType) : bool
    {
        return $userType === self::USER_TYPE_CUSTOMER;
    }

    /**
     *  用户平台 1=伊蜜心选; 2=伊的家商城
     * @param null $platform
     */
    public static function platformTranslate($platform = null)
    {
        $map = [
            self::PLATFORM_COMMUNITY => 'community',
            self::PLATFORM_MALL      => 'mall',
        ];

        return is_null($platform) ? $map : ($map[$platform] ?? 'unknown');
    }


}
