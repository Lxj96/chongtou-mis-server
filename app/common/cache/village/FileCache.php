<?php
/**
 * Description:
 * File: FileCache.php
 * User: Lxj
 * DateTime: 2022-11-13 23:42
 */

namespace app\common\cache\village;


use think\facade\Cache;

class FileCache
{
    /**
     * 缓存key
     *
     * @param mixed $file_id 文件id、文件统计key
     *
     * @return string
     */
    public static function key($file_id)
    {
        return 'village_file:' . $file_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $file_id 文件id、文件统计key
     * @param array $file 文件信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($file_id, $file, $ttl = 86400)
    {
        return Cache::set(self::key($file_id), $file, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $file_id 文件id、文件统计key
     *
     * @return array
     */
    public static function get($file_id)
    {
        return Cache::get(self::key($file_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $file_id 文件id、文件统计key
     *
     * @return bool
     */
    public static function del($file_id)
    {
        return Cache::delete(self::key($file_id));
    }
}