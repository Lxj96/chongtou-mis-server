<?php
/**
 * Description:
 * File: SystemCache.php
 * User: Lxj
 * DateTime: 2022-11-13 01:05
 */

namespace app\common\cache\admin;


use think\facade\Cache;

class SystemCache
{
    /**
     * 缓存key
     *
     * @param int $id id
     *
     * @return string
     */
    public static function key($id)
    {
        return 'system_info:' . $id;
    }

    /**
     * 缓存设置
     *
     * @param int $id id
     * @param array $data 设置信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($id, $data, $ttl = 86400)
    {
        return Cache::set(self::key($id), $data, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $id id
     *
     * @return array 概况信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param int $id id
     *
     * @return bool
     */
    public static function del($id)
    {
        return Cache::delete(self::key($id));
    }
}