<?php
/**
 * Description:
 * File: ProjectValidate.php
 * User: Lxj
 * DateTime: 2022-11-14 19:23
 */

namespace app\common\validate\village;


use think\Validate;

class ProjectValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'name' => ['require'],
        'address' => ['require'],
        'status' => ['require', 'in:0,1,2'],
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'name' => '请输入项目名称',
        'address' => '请输入项目地址',
        'status' => '请选择项目状态',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'name', 'address', 'status'],
        'edit' => ['id', 'village_id', 'name', 'address', 'status'],
        'del' => ['ids'],
    ];
}