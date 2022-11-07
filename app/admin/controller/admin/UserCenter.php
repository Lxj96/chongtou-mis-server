<?php
/**
 * Description: 个人中心控制器
 * File: UserCenter.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\model\admin\MenuModel;
use app\common\service\admin\UserCenterService;
use app\common\utils\DatetimeUtils;
use app\common\validate\admin\UserCenterValidate;
use think\response\Json;

class UserCenter
{
    /**
     * 我的信息
     * @return  Json
     */
    public function index()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserCenterValidate::class)->scene('info')->check($param);

        $data = UserCenterService::info($param['admin_user_id']);

        return success($data);
    }

    /**
     * 修改信息
     * @return  Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['avatar_id'] = input('avatar_id/d', 0);
        $param['username'] = input('username/s', '');
        $param['nickname'] = input('nickname/s', '');
        $param['phone'] = input('phone/s', '');
        $param['email'] = input('email/s', '');

        validate(UserCenterValidate::class)->scene('edit')->check($param);

        $data = UserCenterService::edit($param);

        return success($data);
    }

    /**
     * 修改密码
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\SaveErrorMessage
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pwd()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['password_old'] = input('password_old/s', '');
        $param['password_new'] = input('password_new/s', '');

        validate(UserCenterValidate::class)->scene('pwd')->check($param);

        $data = UserCenterService::pwd($param);

        return success($data);
    }

    /**
     * 我的日志
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function log()
    {
        $admin_user_id = admin_user_id();
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $log_type = input('log_type/d', 0);
        $menu_url = input('menu_url/s', '');
        $menu_name = input('menu_name/s', '');
        $admin_menu_id = input('admin_menu_id/s', '');
        $request_ip = input('request_ip/s', '');
        $request_region = input('request_region/s', '');
        $request_isp = input('request_isp/s', '');
        $create_time_start = input('create_time_start/s', '');
        $create_time_end = input('create_time_end/s', '');

        validate(UserCenterValidate::class)->scene('log')->check(['admin_user_id' => $admin_user_id]);

        // 构建查询条件
        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($log_type) $where[] = ['log_type', '=', $log_type];
        // 菜单相关查询条件
        if (!empty($menu_url)) $menu_where[] = ['menu_url', 'like', '%' . $menu_url . '%'];
        if (!empty($menu_name)) $menu_where[] = ['menu_name', 'like', '%' . $menu_name . '%'];
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();
        if (!empty($menu_where)) {
            $admin_menu_ids = $MenuModel->where($menu_where)->column($MenuPk);
            $where[] = [$MenuPk, 'in', $admin_menu_ids];
        }
        if (!empty($admin_menu_id)) $where[] = [$MenuPk, 'in', $admin_menu_id];
        if (!empty($request_ip)) $where[] = ['request_ip', 'like', '%' . $request_ip . '%'];
        if (!empty($request_region)) $where[] = ['request_region', 'like', '%' . $request_region . '%'];
        if (!empty($request_isp)) $where[] = ['request_isp', 'like', '%' . $request_isp . '%'];
        if (!empty($create_time_start) && !empty($create_time_end)) $where[] = ['create_time', 'between time', [$create_time_start, DatetimeUtils::dateEndTime($create_time_end)]];

        $data = UserCenterService::log($where, $current, $pageSize, $order);

        return success($data);
    }
}
