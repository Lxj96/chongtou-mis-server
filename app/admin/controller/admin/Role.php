<?php
/**
 * Description: 角色管理控制器
 * File: Role.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\MissException;
use app\common\service\admin\MenuService;
use app\common\service\admin\RoleService;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use think\response\Json;

class Role
{
    /**
     * 菜单列表
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menu()
    {
        $data['list'] = MenuService::list('tree');

        return success($data);
    }

    /**
     * 角色列表
     *
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $role_name = input('role_name/s', '');
        $role_desc = input('role_desc/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($role_name)) $where[] = ['role_name', 'like', '%' . $role_name . '%'];
        if (!empty($role_desc)) $where[] = ['role_desc', 'like', '%' . $role_desc . '%'];

        $data = RoleService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 角色信息
     * @param $id
     * @return Json
     * @throws MissException
     */
    public function read($id)
    {
        $param['admin_role_id'] = $id;

        validate(RoleValidate::class)->scene('info')->check($param);

        $data = RoleService::info($param['admin_role_id']);
        if (empty($data)) {
            throw new MissException();
        }

        return success($data);
    }

    /**
     * 角色添加
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function save()
    {
        $param['admin_menu_ids'] = input('admin_menu_ids/a', '');
        $param['role_name'] = input('role_name/s', '');
        $param['role_desc'] = input('role_desc/s', '');
        $param['role_sort'] = input('role_sort/d', 250);

        validate(RoleValidate::class)->scene('add')->check($param);

        $data = RoleService::add($param);

        return success($data);
    }

    /**
     * 角色修改
     * @param $id
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function update($id)
    {
        $param['admin_role_id'] = $id;
        $param['admin_menu_ids'] = input('admin_menu_ids/a', '');
        $param['role_name'] = input('role_name/s', '');
        $param['role_desc'] = input('role_desc/s', '');
        $param['role_sort'] = input('role_sort/d', 250);

        validate(RoleValidate::class)->scene('edit')->check($param);

        $data = RoleService::edit($param);

        return success($data);
    }

    /**
     * 角色删除
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(RoleValidate::class)->scene('del')->check($param);

        $data = RoleService::del($param['ids']);

        return success($data);
    }

    /**
     * 角色是否禁用
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function disable()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_disable'] = input('is_disable/b', false);

        validate(RoleValidate::class)->scene('disable')->check($param);

        $data = RoleService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * 角色用户
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function user()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $admin_role_id = input('admin_role_id/d', '');

        validate(RoleValidate::class)->scene('user')->check(['admin_role_id' => $admin_role_id]);

        $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

        $data = RoleService::user($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 角色用户解除
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userRemove()
    {
        $param['admin_role_id'] = input('admin_role_id/d', '');
        $param['admin_user_id'] = input('admin_user_id/d', '');

        validate(RoleValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = RoleService::userRemove($param);

        return success($data);
    }
}
