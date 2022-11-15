<?php
/**
 * Description: 科室文档目录缓存
 * File: FileGroupCache.php
 * User: Lxj
 * DateTime: 2022-11-13 21:04
 */

namespace app\common\cache\department;


use think\facade\Cache;

class FileGroupCache
{
    /**
     * 缓存key
     *
     * @param int $flag
     *
     * @return string
     */
    public static function key($flag)
    {
        return 'department_file_group:' . $flag;
    }

    /**
     * 缓存设置
     *
     * @param int $flag
     * @param array $data 设置信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($flag, $data, $ttl = 86400)
    {
        return Cache::set(self::key($flag), $data, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $flag
     *
     * @return array 概况信息
     */
    public static function get($flag)
    {
        return Cache::get(self::key($flag));
    }

    /**
     * 缓存删除
     *
     * @param int $flag
     *
     * @return bool
     */
    public static function del($flag)
    {
        return Cache::delete(self::key($flag));
    }
}