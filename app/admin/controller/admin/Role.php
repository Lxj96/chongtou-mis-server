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
use think\facade\Db;
use think\response\Json;

class Role
{
    /**
     * 菜单列表
     * @return Json
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
     */
    public function index()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $admin_role_id = input('admin_role_id/s', '');
        $is_disable = input('is_disable/b');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        // 构建查询条件
        $where = [];
        if (!empty($admin_role_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(admin_role_id,'" . $admin_role_id . "')")];
        if (!empty($search_words)) $where[] = ['role_name|role_desc', 'like', '%' . $search_words . '%'];
        if (is_bool($is_disable)) {
            $where[] = ['is_disable', '=', $is_disable];
        }

        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = RoleService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 角色信息
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['admin_role_id'] = input('get.admin_role_id/d', 0);
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
     * @return Json
     */
    public function update()
    {
        $param['admin_role_id'] = input('admin_role_id/d', 0);
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
