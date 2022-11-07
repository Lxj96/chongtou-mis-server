<?php
/**
 * Description: 用户日志验证器
 * File: UserLogValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\admin;

use think\Validate;

class UserLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'admin_user_log_id' => ['require'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'id' => ['admin_user_log_id'],
        'info' => ['admin_user_log_id'],
        'del' => ['ids'],
    ];
}
