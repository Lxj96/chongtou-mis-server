<?php
/**
 * Description: 文件设置缓存
 * File: SettingCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\file;

use think\facade\Cache;

class SettingCache
{
    /**
     * 缓存key
     *
     * @param int $setting_id 文件设置id
     *
     * @return string
     */
    public static function key($setting_id)
    {
        return 'file_setting:' . $setting_id;
    }

    /**
     * 缓存设置
     *
     * @param int $setting_id 文件设置id
     * @param array $file_setting 文件设置信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($setting_id, $file_setting, $ttl = 7 * 86400)
    {
        return Cache::set(self::key($setting_id), $file_setting, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $setting_id 文件设置id
     *
     * @return array 文件设置信息
     */
    public static function get($setting_id)
    {
        return Cache::get(self::key($setting_id));
    }

    /**
     * 缓存删除
     *
     * @param int $setting_id 文件设置id
     *
     * @return bool
     */
    public static function del($setting_id)
    {
        return Cache::delete(self::key($setting_id));
    }
}
