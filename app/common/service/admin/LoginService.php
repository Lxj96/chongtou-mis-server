<?php
/**
 * Description: 登录退出
 * File: LoginService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

class LoginService
{
    /**
     * 登录
     *
     * @param array $param 登录信息
     *
     * @return array
     */
    public static function login($param)
    {
        return UserService::login($param);
    }

    /**
     * 刷新Token
     * @param integer $admin_user_id 用户ID
     * @return array
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function refresh($admin_user_id)
    {
        return UserService::refresh($admin_user_id);
    }


    /**
     * 退出
     *
     * @param int $admin_user_id 用户id
     *
     * @return array
     */
    public static function logout($admin_user_id)
    {
        return UserService::logout($admin_user_id);
    }
}
