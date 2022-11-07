<?php
/**
 * Description: 菜单管理
 * File: MenuService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use think\facade\Config;
use app\common\cache\admin\MenuCache;
use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class MenuService
{
    /**
     * 菜单列表
     *
     * @param string $type list列表，tree树形
     * @param array $where 搜索条件
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function list($type = 'list', $where = [])
    {
        if ($where) {
            $model = new MenuModel();
            $pk = $model->getPk();

            $field = $pk . ',menu_pid,menu_name,menu_url,menu_sort,is_unauth,is_unlogin,is_disable';

            $order = ['menu_sort' => 'asc', $pk => 'desc'];

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
        }
        else {
            $key = $type;
            $data = MenuCache::get($key);
            if (empty($data)) {
                $model = new MenuModel();
                $pk = $model->getPk();

                $field = $pk . ',menu_pid,menu_name,menu_url,menu_sort,is_unauth,is_unlogin,is_disable';

                $order = ['menu_sort' => 'desc', $pk => 'asc'];

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();

                if ($type == 'tree') {
                    $data = self::toTree($data, 0);
                }

                MenuCache::set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 菜单信息
     *
     * @param integer|string $id 菜单id
     * @param bool $exce 不存在是否抛出异常
     *
     * @return array
     * @throws MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function info($id = 0, $exce = true)
    {
        if (empty($id)) {
            $id = menu_url();
        }

        $info = MenuCache::get($id);
        if (empty($info)) {
            $model = new MenuModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            }
            else {
                $where[] = ['menu_url', '=', $id];
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    throw new MissException('菜单不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            //对pid 获取当前id所有层级关系
            $list = self::list();
            $info['menu_pid_all'] = getCascaderAllByID($info['menu_pid'], $list, 'admin_menu_id', 'menu_pid');
            MenuCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 菜单添加
     *
     * @param array $param 菜单信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $add = false;
        $add_arr = ['index' => '列表', 'read' => '信息', 'save' => '添加', 'update' => '修改', 'delete' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = 'add_' . $k;
            if ($param[$add_key]) {
                $add = true;
            }
        }

        $model = new MenuModel();
        $pk = $model->getPk();

        if ($add) {
            if (empty($param['menu_url'])) {
                throw new SaveErrorMessage('请输入菜单链接：应用/控制器，不含操作');
            }

            $model->startTrans();
            try {
                $id = $model->strict(false)->insertGetId($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = 'add_' . $k;
                    if ($param[$add_key]) {
                        $add_where = [];
                        $add_where[] = ['menu_url', '=', $param['menu_url'] . '/' . $k];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid'] = $id;
                            $add_temp['menu_name'] = $param['menu_name'] . $v;
                            $add_temp['menu_url'] = $param['menu_url'] . '/' . $k;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    $model->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                $model->commit();
            } catch (\Exception $e) {
                $model->rollback();
                throw new SaveErrorMessage('添加失败：' . $e->getMessage() . ':' . $e->getLine());

            }
        }
        else {
            $id = $model->strict(false)->insertGetId($param);
            if (empty($id)) {
                throw new SaveErrorMessage('添加失败');
            }
        }

        MenuCache::del();

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 菜单修改
     *
     * @param array $param 菜单信息
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function edit($param)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $info = self::info($id);

        $param['update_time'] = datetime();

        $add = false;
        $add_arr = $edit_arr = ['index' => '列表', 'read' => '信息', 'save' => '添加', 'update' => '修改', 'delete' => '删除'];
        foreach ($add_arr as $k => $v) {
            $add_key = 'add_' . $k;
            if ($param[$add_key]) {
                $add = true;
            }
        }

        $edit = false;
        foreach ($edit_arr as $k => $v) {
            $edit_key = 'edit_' . $k;
            if ($param[$edit_key]) {
                $edit = true;
            }
        }

        if ($add || $edit) {
            if (empty($param['menu_url'])) {
                throw new SaveErrorMessage('请输入菜单链接：应用/控制器，不含操作');
            }

            $model->startTrans();
            try {
                $model->strict(false)->where($pk, $id)->update($param);

                $add_data = [];
                foreach ($add_arr as $k => $v) {
                    $add_key = 'add_' . $k;
                    if ($param[$add_key]) {
                        $add_where = [];
                        $add_where[] = ['menu_pid', '=', $id];
                        $add_where[] = ['menu_url', '=', $param['menu_url'] . '/' . $k];
                        $add_menu = $model->field($pk)->where($add_where)->find();
                        if (empty($add_menu)) {
                            $add_temp = [];
                            $add_temp['menu_pid'] = $id;
                            $add_temp['menu_name'] = $param['menu_name'] . $v;
                            $add_temp['menu_url'] = $param['menu_url'] . '/' . $k;
                            $add_temp['create_time'] = datetime();
                            $add_data[] = $add_temp;
                        }
                    }
                }
                if ($add_data) {
                    $model->insertAll($add_data);
                }
                $param['add_data'] = $add_data;

                $edit_data = [];
                foreach ($edit_arr as $k => $v) {
                    $edit_key = 'edit_' . $k;
                    if ($param[$edit_key]) {
                        $edit_where = [];
                        $edit_where[] = ['menu_pid', '=', $id];
                        $edit_where[] = ['menu_url', 'like', '%/' . $k];
                        $edit_menu = $model->field($pk)->where($edit_where)->find();
                        if ($edit_menu) {
                            $edit_menu->toArray();
                            $edit_temp = [];
                            $edit_temp['menu_pid'] = $id;
                            $edit_temp['menu_name'] = $param['menu_name'] . $v;
                            $edit_temp['menu_url'] = $param['menu_url'] . '/' . $k;
                            $edit_temp['update_time'] = datetime();
                            $edit_data[] = $edit_temp;
                            $model->where($pk, $edit_menu[$pk])->update($edit_temp);
                        }
                    }
                }
                $param['edit_data'] = $edit_data;

                $model->commit();
            } catch (\Exception $e) {
                $model->rollback();
                throw new SaveErrorMessage('修改失败：' . $e->getMessage() . ':' . $e->getLine());
            }
        }
        else {
            $res = $model->strict(false)->where($pk, $id)->update($param);
            if (empty($res)) {
                throw new SaveErrorMessage('修改失败');
            }
        }

        MenuCache::del([$id, $info['menu_url']]);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 菜单删除
     *
     * @param array $ids 菜单id
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function del($ids)
    {
        foreach ($ids as $v) {
            self::info($v);
        }

        $update['delete_time'] = datetime();

        $model = new MenuModel();
        $pk = $model->getPk();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['menu_url'];
        }
        MenuCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单修改上级
     *
     * @param array $ids 菜单id
     * @param int $menu_pid 菜单pid
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function pid($ids, $menu_pid)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['menu_pid'] = $menu_pid;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['menu_url'];
        }
        MenuCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否无需登录
     *
     * @param array $ids 菜单id
     * @param int $is_unlogin 是否无需登录
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function unlogin($ids, $is_unlogin)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_unlogin'] = $is_unlogin;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['menu_url'];
        }
        MenuCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否无需权限
     *
     * @param array $ids 菜单id
     * @param int $is_unauth 是否无需权限
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function unauth($ids, $is_unauth)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_unauth'] = $is_unauth;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['menu_url'];
        }
        MenuCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单是否禁用
     *
     * @param array $ids 菜单id
     * @param int $is_disable 是否禁用
     *
     * @return array
     * @throws MissException
     * @throws SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function disable($ids, $is_disable)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $update['is_disable'] = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $ids_arr = $ids;
        foreach ($ids as $v) {
            $info = self::info($v);
            $ids_arr[] = $info['menu_url'];
        }
        MenuCache::del($ids_arr);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 菜单角色
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function role($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        return RoleService::list($where, $current, $pageSize, $order, $field);
    }

    /**
     * 菜单角色解除
     *
     * @param array $param 菜单id，角色id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function roleRemove($param)
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();
        $admin_menu_id = $param[$MenuPk];

        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $admin_role_id = $param[$RolePk];

        $role = RoleService::info($admin_role_id);
        $admin_menu_ids = $role['admin_menu_ids'];
        foreach ($admin_menu_ids as $k => $v) {
            if ($admin_menu_id == $v) {
                unset($admin_menu_ids[$k]);
            }
        }
        if (empty($admin_menu_ids)) {
            $admin_menu_ids = str_join('');
        }
        else {
            $admin_menu_ids = str_join(implode(',', $admin_menu_ids));
        }

        $update['update_time'] = datetime();
        $update['admin_menu_ids'] = $admin_menu_ids;

        $res = $RoleModel->where($RolePk, $admin_role_id)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        RoleCache::del($admin_role_id);

        $update[$MenuPk] = $admin_menu_id;
        $update[$RolePk] = $admin_role_id;

        return $update;
    }

    /**
     * 菜单用户
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function user($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        return UserService::list($where, $current, $pageSize, $order, $field);
    }

    /**
     * 菜单用户解除
     *
     * @param array $param 菜单id，用户id
     *
     * @return array
     * @throws SaveErrorMessage
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function userRemove($param)
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();
        $admin_menu_id = $param[$MenuPk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $admin_user_id = $param[$UserPk];

        $user = UserService::info($admin_user_id);
        $admin_menu_ids = $user['admin_menu_ids'];
        foreach ($admin_menu_ids as $k => $v) {
            if ($admin_menu_id == $v) {
                unset($admin_menu_ids[$k]);
            }
        }
        if (empty($admin_menu_ids)) {
            $admin_menu_ids = str_join('');
        }
        else {
            $admin_menu_ids = str_join(implode(',', $admin_menu_ids));
        }

        $update['update_time'] = datetime();
        $update['admin_menu_ids'] = $admin_menu_ids;

        $res = $UserModel->where($UserPk, $admin_user_id)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        UserCache::upd($admin_user_id);

        $update[$MenuPk] = $admin_menu_id;
        $update[$UserPk] = $admin_user_id;

        return $update;
    }

    /**
     * 菜单获取所有子级
     *
     * @param array $menu 菜单列表
     * @param int $admin_menu_id 菜单id
     *
     * @return array
     */
    public static function getChildren($menu, $admin_menu_id)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $children = [];
        foreach ($menu as $v) {
            if ($v['menu_pid'] == $admin_menu_id) {
                $children[] = $v[$pk];
                $children = array_merge($children, self::getChildren($menu, $v[$pk]));
            }
        }

        return $children;
    }

    /**
     * 菜单列表转树形
     *
     * @param array $menu 菜单列表
     * @param int $menu_pid 菜单pid
     *
     * @return array
     */
    public static function toTree($menu, $menu_pid)
    {
        $model = new MenuModel();
        $pk = $model->getPk();

        $tree = [];
        foreach ($menu as $v) {
            if ($v['menu_pid'] == $menu_pid) {
                $v['children'] = self::toTree($menu, $v[$pk]);
                if (empty($v['children'])) unset($v['children']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 菜单url列表
     *
     * @return array
     */
    public static function urlList()
    {
        $key = 'urlList';
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $list = $model->column('menu_url');
            $list = array_filter($list);

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单无需登录url列表
     *
     * @return array
     */
    public static function unloginUrl()
    {
        $key = 'unloginUrl';
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $list = $model->where('is_unlogin', 1)->column('menu_url');
            $unlogin = Config::get('admin.menu_is_unlogin');
            $list = array_merge($list, $unlogin);
            $list = array_unique(array_filter($list));

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单无需权限url列表
     *
     * @return array
     */
    public static function unauthUrl()
    {
        $key = 'unauthUrl';
        $list = MenuCache::get($key);
        if (empty($list)) {
            $model = new MenuModel();

            $list = $model->where('is_unauth', 1)->column('menu_url');
            $unlogin = self::unloginUrl();
            $unauth = Config::get('admin.menu_is_unauth');
            $list = array_merge($list, $unlogin, $unauth);
            $list = array_unique(array_filter($list));

            MenuCache::set($key, $list);
        }

        return $list;
    }

    /**
     * 菜单无需限率url列表
     *
     * @return array
     */
    public static function unrateUrl()
    {
        $key = 'unrateUrl';
        $list = MenuCache::get($key);
        if (empty($list)) {
            $unrate = Config::get('admin.menu_is_unrate');
            $list = array_unique(array_filter($unrate));

            MenuCache::set($key, $list);
        }

        return $list;
    }
}
