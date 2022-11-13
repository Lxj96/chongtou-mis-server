<?php
/**
 * Description:
 * File: CadresValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 11:54
 */

namespace app\common\validate\village;


use think\Validate;

class CadresValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'organization' => ['require'],
        'name' => ['require'],
        'duties' => ['require'],
        'phone' => ['mobile'],
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'organization' => '请选择组织名称',
        'name' => '请输入姓名',
        'duties' => '请输入职务',
        'phone' => '请输入正确的联系电话',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'organization', 'name', 'duties', 'phone'],
        'edit' => ['id', 'village_id', 'organization', 'name', 'duties', 'phone'],
        'del' => ['ids'],
    ];

}