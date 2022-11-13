<?php
/**
 * Description:
 * File: SystemValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 01:00
 */

namespace app\common\validate\admin;


use think\Validate;

class SystemValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'id' => ['require', 'integer'],
        'content' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'content.require' => '请输入概况信息',
    ];

    // 验证场景
    protected $scene = [
        'edit' => ['id', 'remark'],
    ];
}