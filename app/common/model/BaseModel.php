<?php
/**
 * Description: 模型基类
 * File: BaseModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;


class BaseModel extends Model
{

    //生成数据库字段缓存  命令行执行 php think optimize:schema
    use SoftDelete;

    protected $hidden = ['delete_time'];

    protected $deleteTime = 'delete_time';

    //protected $defaultSoftDelete = 0;

    // 对新增/修改操作者自动完成
    /*protected $insert = ['creator'];
    protected $update = ['updater'];

    protected function setCreatorAttr()
    {
        $userData = session('userData');
        return isset($userData['user_name']) ? $userData['user_name'] . '-' . $userData['xm'] : null;
    }

    protected function setUpdaterAttr($value = '', $data = [])
    {
        $userData = session('userData');
        return isset($userData['user_name']) ? $userData['user_name'] . '-' . $userData['xm'] : null;
    }

    protected function filePrefixUrl($value)
    {
        $result = Request::instance()->domain() . '/' . $value;
        return $result;
    }*/
}