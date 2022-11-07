<?php
/**
 * Description: 微信设置缓存
 * File: WechatCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\setting;

use think\facade\Cache;

class WechatCache
{
    /**
     * 缓存key
     *
     * @param int $setting_wechat_id 微信设置id
     *
     * @return string
     */
    public static function key($setting_wechat_id)
    {
        return 'setting_wechat:' . $setting_wechat_id;
    }

    /**
     * 缓存设置
     *
     * @param int $setting_wechat_id 微信设置id
     * @param array $setting_wechat 微信设置信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($setting_wechat_id, $setting_wechat, $ttl = 86400)
    {
        return Cache::set(self::key($setting_wechat_id), $setting_wechat, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $setting_wechat_id 微信设置id
     *
     * @return array 微信设置信息
     */
    public static function get($setting_wechat_id)
    {
        return Cache::get(self::key($setting_wechat_id));
    }

    /**
     * 缓存删除
     *
     * @param int $setting_wechat_id 微信设置id
     *
     * @return bool
     */
    public static function del($setting_wechat_id)
    {
        return Cache::delete(self::key($setting_wechat_id));
    }
}
