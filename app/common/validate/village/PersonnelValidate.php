<?php
/**
 * Description: 行政村人员
 * File: PersonnelValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 14:02
 */

namespace app\common\validate\village;


use think\Validate;

class PersonnelValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'name' => ['require'],
        'idcard' => ['require', 'idCard'],
        'sex' => ['require', 'in:男,女'],
        'birthday' => ['require'],
        'nationality' => ['require'],
        'phone' => ['mobile'],
        'is_lock' => ['require', 'boolean'],
        'is_freeze' => ['require', 'boolean'],
        'is_current_address' => ['require', 'boolean'],
        'is_often' => ['require', 'boolean'],
        'is_alone' => ['require', 'boolean'],
        'is_voter' => ['require', 'boolean'],
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'name' => '请输入姓名',
        'idcard' => '请输入正确的身份证号',
        'sex' => '请正确选择性别',
        'birthday' => '请输入出生日期',
        'nationality' => '请选择民族',
        'phone' => '请输入正确的电话号码'

    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'name', 'idcard', 'sex', 'birthday', 'nationality', 'phone', 'is_lock', 'is_freeze', 'is_current_address', 'is_often', 'is_alone', 'is_voter'],
        'edit' => ['id', 'village_id', 'name', 'idcard', 'sex', 'birthday', 'nationality', 'phone', 'is_lock', 'is_freeze', 'is_current_address', 'is_often', 'is_alone', 'is_voter'],
        'del' => ['ids'],
    ];
}