<?php
/**
 * Description: 公告管理缓存
 * File: NoticeCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\admin;

use think\facade\Cache;

class NoticeCache
{
    /**
     * 缓存key
     *
     * @param int $admin_notice_id 公告id
     *
     * @return string
     */
    public static function key($admin_notice_id)
    {
        return 'admin_notice:' . $admin_notice_id;
    }

    /**
     * 缓存设置
     *
     * @param int $admin_notice_id 公告id
     * @param array $admin_notice 公告信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($admin_notice_id, $admin_notice, $ttl = 86400)
    {
        return Cache::set(self::key($admin_notice_id), $admin_notice, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $admin_notice_id 公告id
     *
     * @return array 公告信息
     */
    public static function get($admin_notice_id)
    {
        return Cache::get(self::key($admin_notice_id));
    }

    /**
     * 缓存删除
     *
     * @param int $admin_notice_id 公告id
     *
     * @return bool
     */
    public static function del($admin_notice_id)
    {
        return Cache::delete(self::key($admin_notice_id));
    }
}
