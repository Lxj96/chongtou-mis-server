<?php
/**
 * Description: 用户日志控制器
 * File: UserLog.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\MissException;
use app\common\model\admin\MenuModel;
use app\common\model\admin\UserModel;
use app\common\service\admin\UserLogService;
use app\common\utils\DatetimeUtils;
use app\common\validate\admin\UserLogValidate;
use think\response\Json;

class UserLog
{
    /**
     * 用户日志列表
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\MissException
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
        $log_type = input('log_type/d', 0);
        $username = input('username/s', '');
        $nickname = input('nickname/s', '');
        $admin_user_id = input('admin_user_id/s', '');
        $menu_url = input('menu_url/s', '');
        $menu_name = input('menu_name/s', '');
        $admin_menu_id = input('admin_menu_id/s', '');
        $request_ip = input('request_ip/s', '');
        $request_region = input('request_region/s', '');
        $request_isp = input('request_isp/s', '');
        $create_time_start = input('create_time_start/s', '');
        $create_time_end = input('create_time_end/s', '');

        // 构建查询条件
        $where = [];
        if ($log_type) $where[] = ['log_type', '=', $log_type];
        // 用户相关查询条件
        if (!empty($username)) $user_where[] = ['username', 'like', '%' . $username . '%'];
        if (!empty($nickname)) $user_where[] = ['nickname', 'like', '%' . $nickname . '%'];
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();
        if (!empty($user_where)) {
            $admin_user_ids = $UserModel->where($user_where)->column($UserPk);
            $where[] = [$UserPk, 'in', $admin_user_ids];
        }
        if (!empty($admin_user_id)) $where[] = [$UserPk, 'in', $admin_user_id];
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

        $data = UserLogService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 用户日志信息
     * @param $id
     * @return Json
     * @throws MissException
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $param['admin_user_log_id'] = $id;

        validate(UserLogValidate::class)->scene('info')->check($param);

        $data = UserLogService::info($param['admin_user_log_id']);
        if (empty($data)) {
            throw new MissException();
        }

        return success($data);
    }

    /**
     * 用户日志删除
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(UserLogValidate::class)->scene('del')->check($param);

        $data = UserLogService::del($param['ids']);

        return success($data);
    }

    /**
     * 用户日志清除
     * @return Json
     * @throws \think\db\exception\DbException
     */
    public function clear()
    {
        $admin_user_id = input('admin_user_id/s', '');
        $username = input('username/s', '');
        $admin_menu_id = input('admin_menu_id/s', '');
        $menu_url = input('menu_url/s', '');
        $date_value = input('date_value/a', '');
        $clean = input('clean/b', false);

        $where = [];
        $admin_user_ids = [];
        if ($admin_user_id) {
            $admin_user_ids = array_merge(explode(',', $admin_user_id), $admin_user_ids);
        }
        if ($username) {
            $UserModel = new UserModel();
            $UserPk = $UserModel->getPk();
            $user_ids = $UserModel->where('username', 'in', $username)->column($UserPk);
            if ($user_ids) {
                $admin_user_ids = array_merge($user_ids, $admin_user_ids);
            }
        }
        if ($admin_user_ids) {
            $where[] = ['admin_user_id', 'in', $admin_user_ids];
        }

        $admin_menu_ids = [];
        if ($admin_menu_id) {
            $admin_menu_ids = array_merge(explode(',', $admin_menu_id), $admin_menu_ids);
        }
        if ($menu_url) {
            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();
            $menu_ids = $MenuModel->where('menu_url', 'in', $menu_url)->column($MenuPk);
            if ($menu_ids) {
                $admin_menu_ids = array_merge($menu_ids, $admin_menu_ids);
            }
        }
        if ($admin_menu_ids) {
            $where[] = ['admin_menu_id', 'in', $admin_menu_ids];
        }

        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = UserLogService::clear($where, $clean);

        return success($data);
    }

    /**
     * 用户日志统计
     */
    public function stat()
    {
        $type = input('type/s', '');
        $date = input('date/a', []);
        $field = input('field/s', 'user');

        $data = [];
        $range = ['total', 'today', 'yesterday', 'thisWeek', 'lastWeek', 'thisMonth', 'lastMonth'];
        if ($type == 'num') {
            $num = [];
            foreach ($range as $v) {
                $num[$v] = UserLogService::statNum($v);
            }
            $data['num'] = $num;
        }
        elseif ($type == 'date') {
            $data['date'] = UserLogService::statDate($date);
        }
        elseif ($type == 'field') {
            $data['field'] = UserLogService::statField($date, $field);
        }
        else {
            $num = [];
            foreach ($range as $v) {
                $num[$v] = UserLogService::statNum($v);
            }

            $data['num'] = $num;
            $data['date'] = UserLogService::statDate($date);
            $data['field'] = UserLogService::statField($date, $field);
        }

        return success($data);
    }
}
