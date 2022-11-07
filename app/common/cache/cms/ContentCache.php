<?php
/**
 * Description: 内容管理缓存
 * File: ContentCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\cms;

use think\facade\Cache;

class ContentCache
{
    /**
     * 缓存键名
     *
     * @param mixed $content_id 内容id
     *
     * @return string
     */
    public static function key($content_id)
    {
        return 'cms_content:' . $content_id;
    }

    /**
     * 缓存写入
     *
     * @param mixed $content_id 内容id
     * @param mixed $content 内容信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($content_id, $content, $ttl = 86400)
    {
        return Cache::set(self::key($content_id), $content, $ttl);
    }

    /**
     * 缓存读取
     *
     * @param mixed $content_id 内容id
     *
     * @return mixed
     */
    public static function get($content_id)
    {
        return Cache::get(self::key($content_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $content_id 内容id
     *
     * @return bool
     */
    public static function del($content_id)
    {
        return Cache::delete(self::key($content_id));
    }

    /**
     * 缓存自增
     *
     * @param string $content_id 内容key
     * @param int $step 步长
     *
     * @return bool
     */
    public static function inc($content_key, $step = 1)
    {
        return Cache::inc(self::key($content_key), $step);
    }
}
