<?php
/**
 * Description: 科室人员
 * File: PersonnelValidate.php
 * User: Lxj
 * DateTime: 2022-11-15 11:58
 */

namespace app\common\validate\department;


use think\Validate;

class PersonnelValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'department_id' => ['require', 'integer'],
        'name' => ['require'],
        'phone' => ['mobile'],
        'duties' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'department_id' => '请选择所属科室',
        'name' => '请输入姓名',
        'phone' => '请输入正确的联系电话',
        'duties' => '请输入职务',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['department_id', 'name', 'phone', 'duties'],
        'edit' => ['id', 'department_id', 'name', 'phone', 'duties'],
        'del' => ['ids'],
    ];
}