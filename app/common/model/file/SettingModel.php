<?php
/**
 * Description: 文件设置模型
 * File: SettingModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\file;

use app\common\model\BaseModel;

class SettingModel extends BaseModel
{
    // 表名
    protected $name = 'file_setting';
    // 表主键
    protected $pk = 'setting_id';
}
