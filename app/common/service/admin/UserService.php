<?php
/**
 * Description: 用户管理
 * File: UserService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\cache\admin\UserCache;
use app\common\exception\AuthException;
use app\common\exception\ParameterException;
use app\common\exception\SaveErrorMessage;
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;
use app\common\service\file\FileService;
use app\common\utils\IpInfoUtils;
use think\facade\Config;

class UserService
{
    /**
     * 用户列表
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
        $model = new UserModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',username,nickname,phone,email,avatar_id,sort,is_disable,is_super,login_num,create_time,login_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $total = $model->where($where)->count($pk);

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

//        foreach ($list as $k => $v) {
//            $list[$k]['avatar_url'] = '';
//            if (!empty($v['avatar_id'])) {
//                $img = FileService::fileUrl($v['img_ids']);
//                if ($img) {
//                    $list[$k]['avatar_url'] = $img;
//                }
//            }
//            unset($list[$k]['avatar_id']);
//        }

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 用户信息
     *
     * @param int $id 用户id
     * @param bool $exce 不存在是否抛出异常
     *
     * @return array
     * @throws AuthException
     */
    public static function info($id, $exce = true)
    {
        $info = UserCache::get($id);
        if (empty($info)) {
            // 获取用户信息
            $model = new UserModel();
            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    throw new AuthException('用户不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();

            $info['avatar_url'] = FileService::fileUrl($info['avatar_id']);
            $info['admin_role_ids'] = str_trim($info['admin_role_ids']);
            $info['admin_menu_ids'] = str_trim($info['admin_menu_ids']);

            // 获取当前用户可查看的菜单url
            if (admin_is_super($id)) { // 在配置文件中设置成了超管
                $menu = $MenuModel->field($MenuPk . ',menu_url')->select()->toArray();
                $menu_ids = array_column($menu, 'admin_menu_id');
                $menu_url = array_column($menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            }
            elseif ($info['is_super'] == 1) {// 数据库中设置成了超管
                $menu = $MenuModel->field($MenuPk . ',menu_url')->where('is_disable', 0)->select()->toArray();
                $menu_ids = array_column($menu, 'admin_menu_id');
                $menu_url = array_column($menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            }
            else {
                $RoleModel = new RoleModel();
                $RolePk = $RoleModel->getPk();

                // 根据用户所在角色获取可查看的菜单id
                $menu_where[] = [$RolePk, 'in', $info['admin_role_ids']];
                $menu_where[] = ['is_disable', '=', 0];
                $menu_ids = $RoleModel->where($menu_where)->column('admin_menu_ids');

                $menu_ids[] = $info['admin_menu_ids'];// 用户表上绑定的菜单id

                // 合并角色表菜单id和用户表菜单id
                $menu_ids_str = implode(',', $menu_ids);
                $menu_ids_arr = explode(',', $menu_ids_str);
                $menu_ids = array_unique($menu_ids_arr);
                $menu_ids = array_filter($menu_ids);

                // 获取菜单url
                // 已授权的菜单
                $where[] = ['admin_menu_id', 'in', $menu_ids];
                $where[] = ['menu_url', '<>', ''];
                $where[] = ['is_disable', '=', 0];
                // 无需权限校验的菜单
                $where_un[] = ['menu_url', '<>', ''];
                $where_un[] = ['is_unauth', '=', 1];
                $where_un[] = ['is_disable', '=', 0];

                $menu_url = $MenuModel->whereOr([$where, $where_un])->column('menu_url');
            }

            $admin_role_ids = $info['admin_role_ids'];
            if (empty($admin_role_ids)) {
                $admin_role_ids = [];
            }
            else {
                $admin_role_ids = explode(',', $info['admin_role_ids']);
                foreach ($admin_role_ids as $k => $v) {
                    $admin_role_ids[$k] = (int)$v;
                }
            }

            $admin_menu_ids = $info['admin_menu_ids'];
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            }
            else {
                $admin_menu_ids = explode(',', $info['admin_menu_ids']);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int)$v;
                }
            }

            if (empty($menu_ids)) {
                $menu_ids = [];
            }
            else {
                foreach ($menu_ids as $k => $v) {
                    $menu_ids[$k] = (int)$v;
                }
            }

            // 合并数据库中获取的菜单url和配置中无需登录或无需权限校验的菜单url => 得到当前用户所能获取的所有菜单url
            $menu_is_unlogin = Config::get('admin.menu_is_unlogin', []);
            $menu_is_unauth = Config::get('admin.menu_is_unauth', []);
            $unlogin_unauth = array_merge($menu_is_unlogin, $menu_is_unauth);
            $menu_url = array_merge($menu_url, $unlogin_unauth);
            $menu_url = array_unique($menu_url);
            sort($menu_url);

            $info['admin_token'] = TokenService::create($info);
            $info['admin_role_ids'] = $admin_role_ids; // 角色id组
            $info['admin_menu_ids'] = $admin_menu_ids; // 用户表保存的菜单id组
            $info['menu_ids'] = $menu_ids; // 用户有权查看的所有菜单id组(不包含配置中的，因为配置中的没有id)
            $info['roles'] = $menu_url;  // 用户有权查看的所有菜单url组(包含配置中的)

            UserCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function add($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $param['password'] = md5($param['password']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 用户修改
     *
     * @param array $param 用户信息
     *
     * @return array
     * @throws AuthException
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        UserCache::upd($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 用户删除
     *
     * @param array $ids 用户id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function del($ids)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            UserCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户分配权限
     *
     * @param array $param 用户信息
     * @param string $method 请求方式
     *
     * @return array
     * @throws AuthException
     * @throws SaveErrorMessage
     */
    public static function rule($param, $method = 'get')
    {
        $model = new UserModel();
        $pk = $model->getPk();

        if ($method == 'get') {
            // 获取用户权限
            $RoleModel = new RoleModel();
            $RolePk = $RoleModel->getPk();

            $admin_user_id = $param[$pk];
            $admin_menu = MenuService::list(); // 菜单列表(不包含配置中的，因为配置中的没有id)
            $admin_role = $RoleModel->field($RolePk . ',role_name')->select()->toArray();
            $admin_user = UserService::info($admin_user_id); // 用户信息

            $menu_ids = $admin_user['menu_ids']; // 用户有权查看的所有菜单id组(不包含配置中的，因为配置中的没有id)
            $admin_menu_ids = $admin_user['admin_menu_ids']; // 用户表保存的菜单id组
            $role_menu_ids = RoleService::getMenuId($admin_user['admin_role_ids']); // 角色有权查看的菜单id组

            foreach ($admin_menu as $k => $v) {
                $admin_menu[$k]['is_check'] = false; // 是否已有当前菜单权限
                $admin_menu[$k]['is_menu'] = false; // 用户表是否已选中当前菜单权限
                $admin_menu[$k]['is_role'] = false; // 用户所属角色是否已有当前菜单权限
                foreach ($menu_ids as $vmis) {
                    if ($v['admin_menu_id'] == $vmis) {
                        $admin_menu[$k]['is_check'] = true;
                    }
                }
                foreach ($admin_menu_ids as $vami) {
                    if ($v['admin_menu_id'] == $vami) {
                        $admin_menu[$k]['is_menu'] = true;
                    }
                }
                foreach ($role_menu_ids as $vrmi) {
                    if ($v['admin_menu_id'] == $vrmi) {
                        $admin_menu[$k]['is_role'] = true;
                    }
                }
            }

            $admin_menu = MenuService::toTree($admin_menu, 0);

            $data[$pk] = $admin_user_id;
            $data['username'] = $admin_user['username'];
            $data['nickname'] = $admin_user['nickname'];
            $data['admin_menu_ids'] = $admin_menu_ids;
            $data['admin_role_ids'] = $admin_user['admin_role_ids'];
            $data['menu_ids'] = $menu_ids;
            $data['admin_role'] = $admin_role;
            $data['admin_menu'] = $admin_menu;

            return $data;
        }
        else {
            $admin_user_id = $param[$pk];
            $admin_role_ids = $param['admin_role_ids'];
            $admin_menu_ids = $param['admin_menu_ids'];

            sort($admin_role_ids);
            sort($admin_menu_ids);

            $update['admin_role_ids'] = str_join(implode(',', $admin_role_ids));
            $update['admin_menu_ids'] = str_join(implode(',', $admin_menu_ids));
            $update['update_time'] = datetime();

            $res = $model->where($pk, $admin_user_id)->update($update);
            if (empty($res)) {
                throw new SaveErrorMessage();
            }

            UserCache::upd($admin_user_id);

            $update[$pk] = $admin_user_id;

            return $update;
        }
    }

    /**
     * 用户重置密码
     *
     * @param array $ids 用户id
     * @param string $password 新密码
     *
     * @return array
     * @throws AuthException
     * @throws SaveErrorMessage
     */
    public static function pwd($ids, $password)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['password'] = md5($password);
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否超管
     *
     * @param array $ids 用户id
     * @param int $is_super 是否超管
     *
     * @return array
     * @throws AuthException
     * @throws SaveErrorMessage
     */
    public static function super($ids, $is_super)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['is_super'] = $is_super;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否禁用
     *
     * @param array $ids 用户id
     * @param int $is_disable 是否禁用
     *
     * @return array
     * @throws AuthException
     * @throws SaveErrorMessage
     */
    public static function disable($ids, $is_disable)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['is_disable'] = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户登录
     *
     * @param array $param 登录信息
     *
     * @return array
     * @throws AuthException
     * @throws ParameterException
     */
    public static function login($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $where[] = ['username|phone|email', '=', $param['username']];
        $where[] = ['password', '=', md5($param['password'])];

        $user = $model->field($pk . ',login_num,is_disable')->where($where)->find();
        if (empty($user)) {
            throw new ParameterException('账号或密码错误');
        }
        $user = $user->toArray();
        if ($user['is_disable']) {
            throw new AuthException('账号已被禁用，请联系管理员');
        }

        // ip信息
        $ip_info = IpInfoUtils::info();
        // 更新用户最新登录信息
        $update['login_ip'] = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time'] = datetime();
        $update['login_num'] = $user['login_num'] + 1;
        $model->where($pk, $user[$pk])->update($update);

        // 存储用户登录日志
        $user_log[$pk] = $user[$pk];
        $user_log['log_type'] = 1;
        $user_log['response_code'] = 200;
        $user_log['response_msg'] = '登录成功';
        UserLogService::add($user_log);

        UserCache::del($user[$pk]);
        $user = self::info($user[$pk]); // 获取用户详细信息

        $admin_user_id = $user[$pk];
        $admin_token = $user['admin_token'];

        return compact($pk, 'admin_token');
    }

    /**
     * 用户退出
     *
     * @param int $id 用户id
     *
     * @return array
     */
    public static function logout($id)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $update['logout_time'] = datetime();

        $model->where($pk, $id)->update($update);

        UserCache::del($id);

        $update[$pk] = $id;

        return $update;
    }

    /**
     * 刷新Token
     * @param int $id 用户id
     * @return array
     * @throws AuthException
     */
    public static function refresh($id)
    {
        $user = self::info($id);// 获取用户详细信息
        // 可能存在  在刷新token的瞬间user缓存失效导致重新生成了token的情况，这样直接使用user缓存中的token
        // 缓存中的token跟header中一致  重新生成
        if ($user['admin_token'] === admin_token()) {
            // 重新生成token
            $admin_token = TokenService::create($user);
            // 更新用户缓存Token
            UserCache::set($id, array_merge($user, ['admin_token' => $admin_token]));
        }

        return compact('admin_token');
    }
}
