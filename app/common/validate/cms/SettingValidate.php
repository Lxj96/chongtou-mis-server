<?php
/**
 * Description: 内容设置验证器
 * File: SettingValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\cms;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'name' => ['length' => '1,80'],
    ];

    // 错误信息
    protected $message = [
        'name.length' => '名称为1到80个字',
    ];

    // 验证场景
    protected $scene = [
        'edit' => ['name'],
    ];
}
