<?php
/**
 * Description: 接口管理缓存
 * File: ApiCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\setting;

use think\facade\Cache;

class ApiCache
{
    /**
     * 缓存key
     *
     * @param mixed $api_id 接口id
     *
     * @return string
     */
    public static function key($api_id)
    {
        return 'api:' . $api_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $api_id 接口id
     * @param array $api 接口信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($api_id = '', $api = [], $ttl = 86400)
    {
        return Cache::set(self::key($api_id), $api, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $api_id 接口id
     *
     * @return array
     */
    public static function get($api_id = '')
    {
        return Cache::get(self::key($api_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $api_id 接口id、key
     *
     * @return bool
     */
    public static function del($api_id = '')
    {
        if (is_array($api_id)) {
            $keys = $api_id;
        }
        else {
            $keys[] = $api_id;
        }

        $keys = array_merge($keys, ['list', 'tree', 'urlList', 'unloginUrl', 'unrateUrl']);
        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }

        return $res;
    }
}
