<?php
/**
 * Description: 地区管理缓存
 * File: RegionCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\setting;

use think\facade\Cache;

class RegionCache
{
    /**
     * 缓存key
     *
     * @param mixed $region_id 地区id
     *
     * @return string
     */
    public static function key($region_id)
    {
        return 'region:' . $region_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $region_id 地区id
     * @param array $region 地区信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($region_id, $region, $ttl = 86400)
    {
        return Cache::set(self::key($region_id), $region, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $region_id 地区id
     *
     * @return array 地区信息
     */
    public static function get($region_id)
    {
        return Cache::get(self::key($region_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $region_id 地区id
     *
     * @return bool
     */
    public static function del($region_id)
    {
        if (is_array($region_id)) {
            $keys = $region_id;
        }
        else {
            $keys[] = $region_id;
        }

        $keys = array_merge($keys, ['tree']);
        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }
        return $res;
    }
}
