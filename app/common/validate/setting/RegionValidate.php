<?php
/**
 * Description: 地区管理验证器
 * File: RegionValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\setting;

use think\Validate;
use app\common\model\setting\RegionModel;

class RegionValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'region_id' => ['require'],
        'region_name' => ['require', 'checkRegionName'],
    ];

    // 错误信息
    protected $message = [
        'region_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['region_id'],
        'info' => ['region_id'],
        'add' => ['region_name'],
        'edit' => ['region_id', 'region_name'],
        'pid' => ['ids'],
        'dele' => ['ids'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkRegionChild');
    }

    // 验证场景定义：修改上级
    protected function scenepid()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkRegionPidNeq');
    }

    // 自定义验证规则：地区上级不能等于本身
    protected function checkRegionPidNeq($value, $rule, $data = [])
    {
        foreach ($data['ids'] as $v) {
            if ($data['region_pid'] == $v) {
                return '地区上级不能等于地区本身';
            }
        }

        return true;
    }

    // 自定义验证规则：地区名称是否已存在
    protected function checkRegionName($value, $rule, $data = [])
    {
        $RegionModel = new RegionModel();
        $RegionPk = $RegionModel->getPk();

        if (isset($data[$RegionPk])) {
            if ($data['region_pid'] == $data[$RegionPk]) {
                return '地区上级不能等于地区本身';
            }
            $where[] = [$RegionPk, '<>', $data[$RegionPk]];
        }
        $where[] = ['region_pid', '=', $data['region_pid']];
        $where[] = ['region_name', '=', $data['region_name']];
        $where[] = ['is_delete', '=', 0];
        $region = $RegionModel->field($RegionPk)->where($where)->find();
        if ($region) {
            return '地区名称已存在：' . $data['region_name'];
        }

        return true;
    }

    // 自定义验证规则：地区是否存在下级地区
    protected function checkRegionChild($value, $rule, $data = [])
    {
        $RegionModel = new RegionModel();
        $RegionPk = $RegionModel->getPk();

        $where[] = ['region_pid', 'in', $data['ids']];
        $where[] = ['is_delete', '=', 0];
        $region = $RegionModel->field($RegionPk)->where($where)->find();
        if ($region) {
            return '地区存在下级地区，无法删除';
        }

        return true;
    }
}
