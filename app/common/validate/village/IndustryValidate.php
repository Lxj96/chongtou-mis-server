<?php
/**
 * Description:
 * File: IndustryValidate.php
 * User: Lxj
 * DateTime: 2022-11-14 19:47
 */

namespace app\common\validate\village;


use think\Validate;

class IndustryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'name' => ['require'],
        'address' => ['require'],
        'owner' => ['require'],
        'phone' => ['require', 'mobile'],
        'license_time' => ['date']
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'name' => '请输入名称',
        'address' => '请输入地点',
        'owner' => '请输入业主姓名',
        'phone' => '请输入联系电话',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'name', 'address', 'owner', 'phone'],
        'edit' => ['id', 'village_id', 'name', 'address', 'owner', 'phone'],
        'del' => ['ids'],
    ];
}