<?php
/**
 * Description: 系统管理缓存
 * File: SettingCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\admin;

use think\facade\Cache;

class SettingCache
{
    /**
     * 缓存key
     *
     * @param int $admin_setting_id 设置id
     *
     * @return string
     */
    public static function key($admin_setting_id)
    {
        return 'admin_setting:' . $admin_setting_id;
    }

    /**
     * 缓存设置
     *
     * @param int $admin_setting_id 设置id
     * @param array $admin_setting 设置信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($admin_setting_id, $admin_setting, $ttl = 86400)
    {
        return Cache::set(self::key($admin_setting_id), $admin_setting, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $admin_setting_id 设置id
     *
     * @return array 设置信息
     */
    public static function get($admin_setting_id)
    {
        return Cache::get(self::key($admin_setting_id));
    }

    /**
     * 缓存删除
     *
     * @param int $admin_setting_id 设置id
     *
     * @return bool
     */
    public static function del($admin_setting_id)
    {
        return Cache::delete(self::key($admin_setting_id));
    }
}
