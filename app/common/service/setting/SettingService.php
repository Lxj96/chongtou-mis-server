<?php
/**
 * Description: 设置
 * File: SettingService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\setting;

use think\facade\Config;
use app\common\cache\setting\SettingCache;
use app\common\model\setting\SettingModel;

class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $id = self::$id;
        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk] = $id;
                $info['token_name'] = Config::get('api.token_name');
                $info['token_key'] = uniqid();
                $model->insert($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return bool|Exception
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $pk = $model->getPk();

        $id = self::$id;

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($id);

        return $param;
    }
}
