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
use app\common\service\admin\UserService;
use app\common\validate\admin\MenuValidate;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use think\facade\Db;
use think\response\Json;

class Menu
{
    /**
     * 菜单列表
     * @return Json
     */
    public function index()
    {
        $menu_pid = input('menu_pid/s', '');
        $menu_id = input('menu_id/s', '');
        $search_words = input('search_words/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($search_words)) $where[] = ['menu_name|menu_url', 'like', '%' . $search_words . '%'];
        if (!empty($menu_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(menu_id,'" . $menu_id . "')")];
        if (!empty($menu_pid)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(menu_pid,'" . $menu_pid . "')")];
        $data['list'] = MenuService::list('tree', $where);

        return success($data);
    }

    /**
     * 菜单信息
     * @param integer $id
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['menu_id'] = input('get.menu_id/d', 0);
        validate(MenuValidate::class)->scene('info')->check($param);

        $data = MenuService::info($param['menu_id']);
        if (empty($data)) {
            throw new MissException('菜单已被删除：' . $param['menu_id']);
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
     * @return Json
     * @throws MissException
     */
    public function update()
    {
        $param['menu_id'] = input('menu_id/d', 0);
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
     * 菜单是否无需日志记录
     * @return Json
     * @throws MissException
     */
    public function unlog()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_unlog'] = input('is_unlog/b', false);

        validate(MenuValidate::class)->scene('unlog')->check($param);

        $data = MenuService::unlog($param['ids'], $param['is_unlog']);

        return success($data);
    }

    /**
     * 菜单角色
     *
     * @return Json
     */
    public function role()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $menu_id = input('menu_id/d', '');

        validate(MenuValidate::class)->scene('role')->check(['menu_id' => $menu_id]);

        $where[] = ['menu_ids', 'like', '%' . str_join($menu_id) . '%'];

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
        $param['menu_id'] = input('menu_id/d', '');
        $param['role_id'] = input('role_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(RoleValidate::class)->scene('id')->check($param);

        $data = MenuService::roleRemove($param);

        return success($data);
    }

    /**
     * 菜单用户
     * @return Json
     */
    public function user()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $role_id = input('role_id/d', '');
        $menu_id = input('menu_id/d', '');

        if ($menu_id) {
            validate(MenuValidate::class)->scene('user')->check(['menu_id' => $menu_id]);

            $where[] = ['menu_ids', 'like', '%' . str_join($menu_id) . '%'];

            $data = UserService::list($where, $current, $pageSize, $order);

            return success($data);
        }
        else {
            validate(RoleValidate::class)->scene('id')->check(['role_id' => $role_id]);

            $where[] = ['role_ids', 'like', '%' . str_join($role_id) . '%'];

            $data = MenuService::user($where, $current, $pageSize, $order);

            return success($data);
        }

    }

    /**
     * 菜单用户解除
     * @return Json
     */
    public function userRemove()
    {
        $param['menu_id'] = input('menu_id/d', '');
        $param['user_id'] = input('user_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = MenuService::userRemove($param);

        return success($data);
    }
}
