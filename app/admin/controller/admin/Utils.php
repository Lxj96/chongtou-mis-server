<?php
/**
 * Description: 实用工具控制器
 * File: Utils.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\UtilsValidate;
use app\common\service\UtilsService;


class Utils
{
    /**
     * 随机字符串
     */
    public function strrand()
    {
        $param['strrand_ids'] = Request::param('ids/a', [1, 2, 3]);
        $param['strrand_len'] = Request::param('len/d', 12);

        validate(UtilsValidate::class)->scene('strrand')->check($param);

        $data = UtilsService::strrand($param['strrand_ids'], $param['strrand_len']);

        return success($data);
    }

    /**
     * 字符串转换
     */
    public function strtran()
    {
        $str = Request::param('str/s', '');

        $data = UtilsService::strtran($str);

        return success($data);
    }

    /**
     * 时间戳转换
     */
    public function timestamp()
    {
        $param['type'] = Request::param('type', '');
        $param['value'] = Request::param('value', '');

        $data = UtilsService::timestamp($param);

        return success($data);
    }

    /**
     * 字节转换
     */
    public function bytetran()
    {
        $param['type'] = Request::param('type', 'B');
        $param['value'] = Request::param('value', 1024);

        $data = UtilsService::bytetran($param);

        return success($data);
    }

    /**
     * IP信息
     */
    public function ipinfo()
    {
        $ip = Request::param('ip/s', '');
        if (empty($ip)) {
            $ip = Request::ip();
        }

        $data = UtilsService::ipinfo($ip);

        return success($data);
    }

    /**
     * 服务器信息
     */
    public function server()
    {
        $data = UtilsService::server();

        return success($data);
    }
}
