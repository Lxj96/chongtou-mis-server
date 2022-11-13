<?php
/**
 * Description: 专职队伍
 * File: TeamValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 17:41
 */

namespace app\common\validate\village;


use think\Validate;

class TeamValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'name' => ['require'],
        'job' => ['require'],
        'phone' => ['require', 'mobile'],
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'name' => '请输入姓名',
        'job' => '请输入工作',
        'phone' => '请输入正确的联系电话',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'name', 'job', 'phone'],
        'edit' => ['id', 'village_id', 'name', 'job', 'phone'],
        'del' => ['ids'],
    ];
}