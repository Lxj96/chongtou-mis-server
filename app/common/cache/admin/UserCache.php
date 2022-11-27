<?php
/**
 * Description: 用户管理缓存
 * File: UserCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\admin;

use think\facade\Cache;
use app\common\service\admin\UserService;
use app\common\service\admin\SettingService;

class UserCache
{
    /**
     * 缓存key
     *
     * @param int $user_id 用户id
     *
     * @return string
     */
    public static function key($user_id)
    {
        return 'admin_user:' . $user_id;
    }

    /**
     * 缓存设置
     *
     * @param int $user_id 用户id
     * @param array $user 用户信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($user_id, $user, $ttl = null)
    {
        if ($ttl === null) {
            $setting = SettingService::info();
            $ttl = $setting['token_exp'] * 3600;
        }

        return Cache::tag('admin')->set(self::key($user_id), $user, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $user_id 用户id
     *
     * @return array 用户信息
     */
    public static function get($user_id)
    {
        return Cache::get(self::key($user_id));
    }

    /**
     * 缓存删除
     *
     * @param int $user_id 用户id
     *
     * @return bool
     */
    public static function del($user_id)
    {
        return Cache::delete(self::key($user_id));
    }

    /**
     * 缓存更新
     *
     * @param int $user_id 用户id
     *
     * @return bool
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function upd($user_id)
    {
        $old = UserService::info($user_id);
        self::del($user_id);

        $new = UserService::info($user_id);
        $new['token'] = $old['token'];

        return self::set($user_id, $new);
    }
}
