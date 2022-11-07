<?php
/**
 * Description: 角色管理验证器
 * File: RoleValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\admin;

use think\Validate;
use app\common\service\admin\RoleService;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class RoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'admin_role_id' => ['require'],
        'role_name' => ['require', 'checkAdminRuleName'],
    ];

    // 错误信息
    protected $message = [
        'role_name.require' => '请输入角色名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['admin_role_id'],
        'info' => ['admin_role_id'],
        'add' => ['role_name'],
        'edit' => ['admin_role_id', 'role_name'],
        'del' => ['ids'],
        'disable' => ['ids'],
        'user' => ['admin_role_id'],
    ];

    // 验证场景定义：删除
    protected function sceneDel()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkAdminRoleMenuUser');
    }

    // 自定义验证规则：角色名称是否已存在
    protected function checkAdminRuleName($value, $rule, $data = [])
    {
        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();

        if (isset($data[$RolePk])) {
            $role_where[] = [$RolePk, '<>', $data[$RolePk]];
        }
        $role_where[] = ['role_name', '=', $data['role_name']];
        $role = $RoleModel->field($RolePk)->where($role_where)->find();
        if ($role) {
            return '角色名称已存在：' . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色是否有菜单或用户
    protected function checkAdminRoleMenuUser($value, $rule, $data = [])
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        foreach ($data['ids'] as $v) {
            $role = RoleService::info($v);
            if ($role['admin_menu_ids']) {
                return '请在[修改]中取消所有菜单后再删除';
            }

            $user_where[] = ['admin_role_ids', 'like', '%' . str_join($v) . '%'];
            $user = $UserModel->field($UserPk)->where($user_where)->find();
            if ($user) {
                return '请在[用户]中解除所有用户后再删除';
            }
        }

        return true;
    }
}
