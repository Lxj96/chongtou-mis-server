<?php
/**
 * Description: 登录退出
 * File: LoginService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\service;

use app\common\service\member\MemberService;

class LoginService
{
    /**
     * 登录
     *
     * @param array $param 登录信息
     * @param string $type 登录方式
     *
     * @return array
     */
    public static function login($param, $type = '')
    {
        return MemberService::login($param, $type);
    }

    /**
     * 微信登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return array
     */
    public static function wechat($userinfo)
    {
        return MemberService::wechat($userinfo);
    }

    /**
     * 退出
     *
     * @param int $member_id 会员id
     *
     * @return array
     */
    public static function logout($member_id)
    {
        return MemberService::logout($member_id);
    }
}
