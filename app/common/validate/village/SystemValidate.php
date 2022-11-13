<?php
/**
 * Description:
 * File: SystemValidate.php
 * User: Lxj
 * DateTime: 2022-11-13 10:12
 */

namespace app\common\validate\village;


use app\common\model\village\VillageSystemModel;
use think\Validate;

class SystemValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'id' => ['require', 'integer'],
        'village_name' => ['require', 'checkVillageName'],
    ];

    // 错误信息
    protected $message = [
        'village_name.require' => '请输入行政村名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['id'],
        'info' => ['id'],
        'add' => ['village_name'],
        'edit' => ['id', 'village_name'],
        'del' => ['ids'],
    ];

    // 验证场景定义：删除
//    protected function sceneDel()
//    {
//        return $this->only(['ids'])
//            ->append('ids', 'checkAdminRoleMenuUser');
//    }

    // 自定义验证规则：行政村名称是否已存在
    protected function checkVillageName($value, $rule, $data = [])
    {
        $model = new VillageSystemModel();

        if (isset($data['id'])) {
            $where[] = ['id', '<>', $data['id']];
        }
        $where[] = ['village_name', '=', $data['village_name']];
        $result = $model->field('id')->where($where)->find();
        if ($result) {
            return '行政村名称已存在：' . $data['village_name'];
        }

        return true;
    }
}