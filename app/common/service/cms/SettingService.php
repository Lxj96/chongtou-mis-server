<?php
/**
 * Description: 内容设置
 * File: SettingService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\cms;

use app\common\cache\cms\SettingCache;
use app\common\model\cms\SettingModel;
use app\common\service\file\FileService;

class SettingService
{
    // 内容设置id
    protected static $id = 1;

    /**
     * 内容设置信息
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
                $info['create_time'] = datetime();
                $model->insert($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();

            $info['logo_url'] = FileService::fileUrl($info['logo_id']);
            $info['off_acc_url'] = FileService::fileUrl($info['off_acc_id']);

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 内容设置修改
     *
     * @param array $param 内容信息
     *
     * @return array
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
