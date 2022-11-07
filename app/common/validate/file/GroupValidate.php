<?php
/**
 * Description: 文件分组验证器
 * File: GroupValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\file;

use think\Validate;
use app\common\model\file\GroupModel;
use app\common\model\file\FileModel;

class GroupValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'group_id' => ['require', 'integer'],
        'group_name' => ['require', 'checkGroupName'],
    ];

    // 错误信息
    protected $message = [
        'group_name.require' => '请输入分组名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['group_id'],
        'info' => ['group_id'],
        'add' => ['group_name'],
        'edit' => ['group_id', 'group_name'],
        'del' => ['ids'],
        'disable' => ['ids'],
    ];

    // 验证场景定义：删除D
    protected function sceneDel()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkGroupFile');
    }

    // 自定义验证规则：分组名称是否已存在
    protected function checkGroupName($value, $rule, $data = [])
    {
        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        if (isset($data[$GroupPk])) {
            $where[] = [$GroupPk, '<>', $data[$GroupPk]];
        }
        $where[] = ['group_name', '=', $data['group_name']];
        $group = $GroupModel->field($GroupPk)->where($where)->find();
        if ($group) {
            return '分组名称已存在：' . $data['group_name'];
        }

        return true;
    }

    // 自定义验证规则：分组是否有文件
    protected function checkGroupFile($value, $rule, $data = [])
    {
        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        $FileModel = new FileModel();
        $FilePk = $FileModel->getPk();

        $where[] = [$GroupPk, 'in', $data['ids']];
        $file = $FileModel->field($FilePk)->where($where)->find();
        if ($file) {
            return '分组下有文件，无法删除';
        }

        return true;
    }
}
