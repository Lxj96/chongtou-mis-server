<?php
/**
 * Description: 用户管理控制器
 * File: User.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\MissException;
use app\common\utils\DatetimeUtils;
use think\facade\Request;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\UserService;
use think\response\Json;

class User
{
    /**
     * 用户列表
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
        $username = input('username/s', '');
        $nickname = input('nickname/s', '');
        $email = input('email/s', '');
        $login_time_start = input('login_time_start/s', '');
        $login_time_end = input('login_time_end/s', '');
        $create_time_start = input('create_time_start/s', '');
        $create_time_end = input('create_time_end/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($username)) $where[] = ['username', 'like', '%' . $username . '%'];
        if (!empty($nickname)) $where[] = ['nickname', 'like', '%' . $nickname . '%'];
        if (!empty($email)) $where[] = ['email', 'like', '%' . $email . '%'];
        if (!empty($login_time_start) && !empty($login_time_end)) $where[] = ['login_time', 'between time', [$login_time_start, DatetimeUtils::dateEndTime($login_time_end)]];
        if (!empty($create_time_start) && !empty($create_time_end)) $where[] = ['create_time', 'between time', [$create_time_start, DatetimeUtils::dateEndTime($create_time_end)]];

        $data = UserService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 用户信息
     * @param integer $id
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $param['admin_user_id'] = $id;

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
     * @throws \app\common\exception\SaveErrorMessage
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
     * @param integer $id
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id)
    {
        $param['admin_user_id'] = $id;
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
     * @throws \app\common\exception\SaveErrorMessage
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
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
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
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
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
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
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
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
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
