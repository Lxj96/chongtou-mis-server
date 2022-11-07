<?php
/**
 * Description: 会员管理缓存
 * File: MemberCache.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\cache\member;

use think\facade\Cache;
use app\common\service\member\MemberService;

class MemberCache
{
    /**
     * 缓存key
     *
     * @param mixed $member_id 会员id、统计时间
     *
     * @return string
     */
    public static function key($member_id)
    {
        return 'member:' . $member_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $member_id 会员id、统计时间
     * @param array $member 会员信息
     * @param int $ttl 有效时间（秒）0永久
     *
     * @return bool
     */
    public static function set($member_id, $member, $ttl = 86400)
    {
        return Cache::set(self::key($member_id), $member, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $member_id 会员id、统计时间
     *
     * @return array 会员信息
     */
    public static function get($member_id)
    {
        return Cache::get(self::key($member_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $member_id 会员id、统计时间
     *
     * @return bool
     */
    public static function del($member_id)
    {
        return Cache::delete(self::key($member_id));
    }

    /**
     * 缓存更新
     *
     * @param int $member_id 会员id
     *
     * @return array 会员信息
     */
    public static function upd($member_id)
    {
        self::del($member_id);

        return MemberService::info($member_id);
    }
}
