<?php
/**
 * Description: 菜单管理控制器
 * File: Menu.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\MissException;
use app\common\service\admin\MenuService;
use app\common\validate\admin\MenuValidate;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use think\response\Json;

class Menu
{
    /**
     * 菜单列表
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $menu_name = input('menu_name/s', '');
        $menu_url = input('menu_url/s', '');
        $admin_menu_id = input('admin_menu_id/d', '');
        $menu_pid = input('menu_pid/d', '');

        // 构建查询条件
        $where = [];
        if (!empty($menu_name)) $where[] = ['menu_name', 'like', '%' . $menu_name . '%'];
        if (!empty($menu_url)) $where[] = ['menu_url', 'like', '%' . $menu_url . '%'];
        if (!empty($admin_menu_id)) {
            $search_exp = strpos($admin_menu_id, ',') ? 'in' : '=';
            $where[] = ['admin_menu_id', $search_exp, $admin_menu_id];
        }
        if (!empty($menu_pid)) {
            $search_exp = strpos($menu_pid, ',') ? 'in' : '=';
            $where[] = ['menu_pid', $search_exp, $menu_pid];
        }

        $data['list'] = MenuService::list('tree', $where);

        return success($data);
    }

    /**
     * 菜单信息
     * @param integer $id
     * @return Json
     * @throws MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $param['admin_menu_id'] = $id;

        validate(MenuValidate::class)->scene('info')->check($param);

        $data = MenuService::info($param['admin_menu_id']);
        if (empty($data)) {
            throw new MissException('菜单已被删除：' . $param['admin_menu_id']);
        }

        return success($data);
    }

    /**
     * 菜单添加
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function save()
    {
        $param['menu_pid'] = input('menu_pid/d', 0);
        $param['menu_name'] = input('menu_name/s', '');
        $param['menu_url'] = input('menu_url/s', '');
        $param['menu_sort'] = input('menu_sort/d', 250);
        $param['add_index'] = input('add_index/b', false);
        $param['add_read'] = input('add_read/b', false);
        $param['add_save'] = input('add_save/b', false);
        $param['add_update'] = input('add_update/b', false);
        $param['add_delete'] = input('add_delete/b', false);

        validate(MenuValidate::class)->scene('add')->check($param);

        $data = MenuService::add($param);

        return success($data);
    }

    /**
     * 菜单修改
     * @param integer $id
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id)
    {
        $param['admin_menu_id'] = $id;
        $param['menu_pid'] = input('menu_pid/d', 0);
        $param['menu_name'] = input('menu_name/s', '');
        $param['menu_url'] = input('menu_url/s', '');
        $param['menu_sort'] = input('menu_sort/d', 250);
        $param['add_index'] = input('add_index/b', false);
        $param['add_read'] = input('add_read/b', false);
        $param['add_save'] = input('add_save/b', false);
        $param['add_update'] = input('add_update/b', false);
        $param['add_delete'] = input('add_delete/b', false);
        $param['edit_index'] = input('edit_index/b', false);
        $param['edit_read'] = input('edit_read/b', false);
        $param['edit_save'] = input('edit_save/b', false);
        $param['edit_update'] = input('edit_update/b', false);
        $param['edit_delete'] = input('edit_delete/b', false);

        validate(MenuValidate::class)->scene('edit')->check($param);

        $data = MenuService::edit($param);

        return success($data);
    }

    /**
     * 菜单删除
     *
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(MenuValidate::class)->scene('del')->check($param);

        $data = MenuService::del($param['ids']);

        return success($data);
    }

    /**
     * 菜单修改上级
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pid()
    {
        $param['ids'] = input('ids/a', []);
        $param['menu_pid'] = input('menu_pid/d', 0);

        validate(MenuValidate::class)->scene('pid')->check($param);

        $data = MenuService::pid($param['ids'], $param['menu_pid']);

        return success($data);
    }

    /**
     * 菜单是否无需登录
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function unlogin()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_unlogin'] = input('is_unlogin/b', false);

        validate(MenuValidate::class)->scene('unlogin')->check($param);

        $data = MenuService::unlogin($param['ids'], $param['is_unlogin']);

        return success($data);
    }

    /**
     * 菜单是否无需权限
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function unauth()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_unauth'] = input('is_unauth/b', false);

        validate(MenuValidate::class)->scene('unauth')->check($param);

        $data = MenuService::unauth($param['ids'], $param['is_unauth']);

        return success($data);
    }

    /**
     * 菜单是否禁用
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function disable()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_disable'] = input('is_disable/b', false);

        validate(MenuValidate::class)->scene('disable')->check($param);

        $data = MenuService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * 菜单角色
     *
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function role()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $admin_menu_id = input('admin_menu_id/d', '');

        validate(MenuValidate::class)->scene('role')->check(['admin_menu_id' => $admin_menu_id]);

        $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

        $data = MenuService::role($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 菜单角色解除
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function roleRemove()
    {
        $param['admin_menu_id'] = input('admin_menu_id/d', '');
        $param['admin_role_id'] = input('admin_role_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(RoleValidate::class)->scene('id')->check($param);

        $data = MenuService::roleRemove($param);

        return success($data);
    }

    /**
     * 菜单用户
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
        $admin_menu_id = input('admin_menu_id/d', '');

        validate(MenuValidate::class)->scene('user')->check(['admin_menu_id' => $admin_menu_id]);

        $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

        $data = MenuService::user($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 菜单用户解除
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userRemove()
    {
        $param['admin_menu_id'] = input('admin_menu_id/d', '');
        $param['admin_user_id'] = input('admin_user_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = MenuService::userRemove($param);

        return success($data);
    }
}
