<?php
/**
 * Description: 控制台
 * File: IndexService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Cache;

class IndexService
{
    /**
     * 首页
     *
     * @return array
     */
    public static function index()
    {
        $data['name'] = 'yylAdmin';
        $data['desc'] = '基于ThinkPHP6和Vue2的极简后台管理系统';
        $data['gitee'] = 'https://gitee.com/skyselang/yylAdmin';
        $data['github'] = 'https://github.com/skyselang/yylAdmin';

        return $data;
    }

    /**
     * 总数统计
     *
     * @return array
     */
    public static function count()
    {
        $key = 'statistics:count';
        $data = Cache::get($key);
        if (empty($data)) {
            $data = [];
            $table = ['file' => '文件', 'region' => '地区'];
            foreach ($table as $k => $v) {
                $temp = [];
                $temp['name'] = $v;
                $temp['count'] = Db::name($k)->whereNull('delete_time')->count();
                $data[] = $temp;
            }
            Cache::set($key, $data, 60);
        }

        return $data;
    }
}
