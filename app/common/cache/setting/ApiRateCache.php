<?php
/**
 * Description: 接口速率缓存
 * File: ApiRateCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\setting;

use think\facade\Cache;

class ApiRateCache
{
    /**
     * 缓存key
     *
     * @param int $member_id 会员id
     * @param string $api_url 接口url
     *
     * @return string
     */
    public static function key($member_id, $api_url)
    {
        return 'apirate:' . $member_id . ':' . $api_url;
    }

    /**
     * 缓存设置
     *
     * @param int $member_id 会员id
     * @param string $api_url 接口url
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($member_id, $api_url, $ttl = 60)
    {
        return Cache::set(self::key($member_id, $api_url), 1, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $member_id 会员id
     * @param string $api_url 接口url
     *
     * @return string
     */
    public static function get($member_id, $api_url)
    {
        return Cache::get(self::key($member_id, $api_url));
    }

    /**
     * 缓存删除
     *
     * @param int $member_id 会员id
     * @param string $api_url 接口url
     *
     * @return bool
     */
    public static function del($member_id, $api_url)
    {
        return Cache::delete(self::key($member_id, $api_url));
    }

    /**
     * 缓存自增
     *
     * @param int $member_id 会员id
     * @param string $api_url 接口url
     *
     * @return bool
     */
    public static function inc($member_id, $api_url)
    {
        return Cache::inc(self::key($member_id, $api_url));
    }
}
