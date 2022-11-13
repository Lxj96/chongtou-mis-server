<?php
/**
 * Description: 用户管理控制器
 * File: User.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\MissException;
use app\common\service\admin\UserService;
use app\common\validate\admin\UserValidate;
use think\facade\Request;
use think\response\Json;

class User
{
    /**
     * 用户列表
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
        $is_super = input('is_super/b');
        $is_disable = input('is_disable/b');
        $search_field = input('search_field/s', '');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);

        // 构建查询条件
        $where = [];
        if ($search_field && $search_words) {
            if (in_array($search_field, ['admin_user_id'])) {
                $search_exp = strpos($search_words, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_words];
            }
            else {
                $where[] = [$search_field, 'like', '%' . $search_words . '%'];
            }
        }
        if (is_bool($is_super)) {
            $where[] = ['is_super', '=', $is_super];
        }
        if (is_bool($is_disable)) {
            $where[] = ['is_disable', '=', $is_disable];
        }

        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = UserService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 用户信息
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['admin_user_id'] = input('get.admin_user_id/d', 0);

        validate(UserValidate::class)->scene('info')->check($param);

        $data = UserService::info($param['admin_user_id']);
        if (empty($data)) {
            throw new MissException();
        }

        return success($data);
    }

    /**
     * 用户添加
     * @return Json
     */
    public function save()
    {
        $param['avatar_id'] = input('avatar_id/d', 0);
        $param['username'] = input('username/s', '');
        $param['nickname'] = input('nickname/s', '');
        $param['password'] = input('password/s', '');
        $param['email'] = input('email/s', '');
        $param['phone'] = input('phone/s', '');
        $param['remark'] = input('remark/s', '');
        $param['sort'] = input('sort/d', 250);

        validate(UserValidate::class)->scene('add')->check($param);

        $data = UserService::add($param);

        return success($data);
    }

    /**
     * 用户修改
     * @return Json
     */
    public function update()
    {
        $param['admin_user_id'] = input('admin_user_id/d', 0);
        $param['avatar_id'] = input('avatar_id/d', 0);
        $param['username'] = input('username/s', '');
        $param['nickname'] = input('nickname/s', '');
        $param['email'] = input('email/s', '');
        $param['phone'] = input('phone/s', '');
        $param['remark'] = input('remark/s', '');
        $param['sort'] = input('sort/d', 250);

        validate(UserValidate::class)->scene('edit')->check($param);

        $data = UserService::edit($param);

        return success($data);
    }

    /**
     * 用户删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(UserValidate::class)->scene('del')->check($param);

        $data = UserService::del($param['ids']);

        return success($data);
    }

    /**
     * 用户 分配|获取 权限
     * @return Json
     */
    public function rule()
    {
        $param['admin_user_id'] = input('admin_user_id/d', '');

        validate(UserValidate::class)->scene('rule')->check($param);

        if (Request::isGet()) {
            $data = UserService::rule($param);
        }
        else {
            $param['admin_role_ids'] = input('admin_role_ids/a', '');
            $param['admin_menu_ids'] = input('admin_menu_ids/a', '');

            $data = UserService::rule($param, Request::method());
        }

        return success($data);
    }

    /**
     * 用户重置密码
     * @return Json
     */
    public function pwd()
    {
        $param['ids'] = input('ids/a', '');
        $param['password'] = input('password/s', '');

        validate(UserValidate::class)->scene('pwd')->check($param);

        $data = UserService::pwd($param['ids'], $param['password']);

        return success($data);
    }

    /**
     * 用户是否超管
     * @return Json
     */
    public function super()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_super'] = input('is_super/b', false);

        validate(UserValidate::class)->scene('super')->check($param);

        $data = UserService::super($param['ids'], $param['is_super']);

        return success($data);
    }

    /**
     * 用户是否禁用
     * @return Json
     */
    public function disable()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_disable'] = input('is_disable/b', false);

        validate(UserValidate::class)->scene('disable')->check($param);

        $data = UserService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }
}
