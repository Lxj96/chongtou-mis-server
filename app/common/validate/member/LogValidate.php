<?php
/**
 * Description: 会员日志验证器
 * File: LogValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\member;

use think\Validate;

class LogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'member_log_id' => ['require'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'id' => ['member_log_id'],
        'info' => ['member_log_id'],
        'dele' => ['ids'],
    ];
}
