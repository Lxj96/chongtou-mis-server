<?php
/**
 * Description: 科室信息
 * File: DepartmentValidate.php
 * User: Lxj
 * DateTime: 2022-11-15 11:23
 */

namespace app\common\validate\department;


use think\Validate;

class DepartmentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'name' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'name' => '请输入科室名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['name'],
        'edit' => ['id', 'name'],
        'del' => ['ids'],
    ];
}