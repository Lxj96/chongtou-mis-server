<?php
/**
 * Description: 党员信息
 * File: PartyMemberValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 17:58
 */

namespace app\common\validate\village;


use think\Validate;

class PartyMemberValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_id' => ['require', 'integer'],
        'name' => ['require'],
        'join_time' => ['require', 'date'],
        'phone' => ['require', 'mobile'],
        'is_out' => ['boolean'],
        'out_address' => ['requireCallback:check_require']
    ];

    // 错误信息
    protected $message = [
        'village_id' => '请选择所属行政村',
        'name' => '请输入姓名',
        'join_time' => '请输入入党时间',
        'phone' => '请输入正确的联系电话',
        'out_address' => '请填写外出地点',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_id', 'name', 'join_time', 'phone', 'address', 'is_out', 'out_address'],
        'edit' => ['id', 'village_id', 'name', 'join_time', 'phone', 'address', 'is_out', 'out_address'],
        'del' => ['ids'],
    ];

    function check_require($value, $data)
    {
        return boolval($data['is_out']);
    }
}