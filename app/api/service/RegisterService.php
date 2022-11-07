<?php
/**
 * Description: 注册
 * File: RegisterService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\service;

use app\common\service\member\MemberService;
use app\common\service\member\LogService;

class RegisterService
{
    /**
     * 账号注册
     *
     * @param array $param 注册信息
     *
     * @return array
     */
    public static function register($param)
    {
        if (empty($param['username'])) {
            $param['username'] = md5(uniqid(mt_rand(), true));
        }
        $data = MemberService::add($param);

        $member_log['member_id'] = $data['member_id'];
        LogService::add($member_log, 1);

        return $data;
    }
}
