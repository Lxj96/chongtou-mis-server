<?php
/**
 * Description: 系统管理模型
 * File: SettingModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class SettingModel extends BaseModel
{
    // 表名
    protected $name = 'admin_setting';
    protected $pk = 'setting_id';

    protected $type = [
        'captcha_switch' => 'boolean',
        'log_switch' => 'boolean',
    ];
}
