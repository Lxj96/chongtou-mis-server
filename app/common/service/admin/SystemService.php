<?php
/**
 * Description:
 * File: SystemService.php
 * User: Lxj
 * DateTime: 2022-11-13 01:02
 */

namespace app\common\service\admin;


use app\common\cache\admin\SystemCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\admin\SystemFileModel;
use app\common\model\admin\SystemModel;
use app\common\service\file\FileService;
use think\facade\Db;

class SystemService
{
    /**
     * 概况信息
     *
     * @return array
     */
    public static function info()
    {

        $info = SystemCache::get(1);
        if (empty($info)) {
            $model = new SystemModel();

            $info = $model->find(1);
            if (empty($info)) {
                throw new MissException('概况信息不存在');
            }

            $info['files'] = FileService::fileArray($info['file_ids']);

            SystemCache::set(1, $info);
        }
        return $info;
    }

    /**
     * 修改信息
     *
     * @param array $param 概况信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {

        $model = new SystemModel();

        $param['update_time'] = datetime();

        $param['file_ids'] = file_ids($param['files']);
        unset($param['files']);
        $res = $model->where('id', $param['id'])->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        SystemCache::del($param['id']);

        return self::info();
    }
}