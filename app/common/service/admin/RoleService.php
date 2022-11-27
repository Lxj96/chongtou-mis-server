<?php
/**
 * Description: 角色管理
 * File: RoleService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\cache\admin\RoleCache;
use app\common\cache\admin\UserCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class RoleService
{
    /**
     * 角色列表
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     */
    public static function list($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',role_name,role_desc,role_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['role_sort' => 'desc', $pk => 'desc'];
        }

        $total = $model->where($where)->count($pk);

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 角色信息
     *
     * @param int $id 角色id
     *
     * @return array
     */
    public static function info($id)
    {
        $info = RoleCache::get($id);
        if (empty($info)) {
            $model = new RoleModel();
            $info = $model->find($id);
            if (empty($info)) {
                throw new MissException('角色不存在：' . $id);
            }
            $info = $info->toArray();

            $menu_ids = str_trim($info['menu_ids']);
            if (empty($menu_ids)) {
                $menu_ids = [];
            }
            else {
                $menu_ids = explode(',', $menu_ids);
                foreach ($menu_ids as $k => $v) {
                    $menu_ids[$k] = (int)$v;
                }
            }
            $info['menu_ids'] = $menu_ids;

            RoleCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 角色添加
     *
     * @param array $param 角色信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function add($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        sort($param['menu_ids']);

        $param['menu_ids'] = implode(',', $param['menu_ids']);
        $param['menu_ids'] = str_join($param['menu_ids']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 角色修改
     *
     * @param array $param 角色信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        sort($param['menu_ids']);

        if (count($param['menu_ids']) > 0) {
            if (empty($param['menu_ids'][0])) {
                unset($param['menu_ids'][0]);
            }
        }

        $param['menu_ids'] = implode(',', $param['menu_ids']);
        $param['menu_ids'] = str_join($param['menu_ids']);
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        RoleCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 角色删除
     *
     * @param array $ids 角色id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function del($ids)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            RoleCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 角色禁用
     *
     * @param array $ids 角色id
     * @param int $is_disable 是否禁用
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function disable($ids, $is_disable)
    {
        $model = new RoleModel();
        $pk = $model->getPk();

        $update['is_disable'] = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            RoleCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 角色用户
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     */
    public static function user($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        return UserService::list($where, $current, $pageSize, $order, $field);
    }

    /**
     * 角色用户解除
     *
     * @param array $param 角色id,用户id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function userRemove($param)
    {
        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();
        $role_id = $param[$RolePk];

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        $user_id = $param[$UserPk];

        $role_ids = [];
        $user = UserService::info($user_id);
        if ($user) {
            $role_ids = $user['role_ids'];
            foreach ($role_ids as $k => $v) {
                if ($role_id == $v) {
                    unset($role_ids[$k]);
                }
            }
        }

        if (empty($role_ids)) {
            $role_ids = str_join('');
        }
        else {
            $role_ids = str_join(implode(',', $role_ids));
        }

        $update['role_ids'] = $role_ids;
        $update['update_time'] = datetime();

        $res = $UserModel->where($UserPk, $user_id)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        UserCache::upd($user_id);

        $update[$RolePk] = $role_id;
        $update[$UserPk] = $user_id;

        return $update;
    }

    /**
     * 角色获取菜单id
     *
     * @param mixed $id 角色id
     *
     * @return array
     */
    public static function getMenuId($id)
    {
        if (empty($id)) {
            return [];
        }

        if (is_numeric($id)) {
            $role_ids[] = $id;
        }
        elseif (is_array($id)) {
            $role_ids = $id;
        }
        else {
            $id = str_trim($id);
            $role_ids = explode(',', $id);
        }

        $menu_ids = [];
        foreach ($role_ids as $v) {
            $info = self::info($v);
            $menu_ids = array_merge($menu_ids, $info['menu_ids']);
        }
        $menu_ids = array_unique($menu_ids);

        sort($menu_ids);

        return $menu_ids;
    }
}
