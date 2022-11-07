<?php
/**
 * Description: 手机验证码缓存
 * File: CaptchaSmsCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\utils;

use think\facade\Cache;

class CaptchaSmsCache
{
    /**
     * 缓存key
     *
     * @param string $phone 手机
     *
     * @return string
     */
    public static function key($phone)
    {
        return 'captcha-phone:' . $phone;
    }

    /**
     * 缓存设置
     *
     * @param int $phone 手机
     * @param string $captcha 验证码
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($phone, $setting, $ttl = 1800)
    {
        return Cache::set(self::key($phone), $setting, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param string $phone 手机
     *
     * @return string 验证码
     */
    public static function get($phone)
    {
        return Cache::get(self::key($phone));
    }

    /**
     * 缓存删除
     *
     * @param string $phone 手机
     *
     * @return bool
     */
    public static function del($phone)
    {
        return Cache::delete(self::key($phone));
    }
}
