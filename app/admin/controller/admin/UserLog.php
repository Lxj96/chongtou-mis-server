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
     */
    public function index()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $search_field = input('search_field/s', '');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        $log_type = input('log_type/d', '');

        // 构建查询条件
        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_words) {
            if (in_array($search_field, ['admin_user_log_id', 'admin_user_id', 'admin_menu_id'])) {
                $search_exp = strpos($search_words, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_words];
            }
            elseif (in_array($search_field, ['username'])) {
                $UserModel = new UserModel();
                $UserPk = $UserModel->getPk();
                $user_exp = strpos($search_words, ',') ? 'in' : '=';
                $user_where[] = [$search_field, $user_exp, $search_words];
                $admin_user_ids = $UserModel->where($user_where)->column($UserPk);
                $where[] = [$UserPk, 'in', $admin_user_ids];
            }
            elseif (in_array($search_field, ['menu_url', 'menu_name'])) {
                $MenuModel = new MenuModel();
                $MenuPk = $MenuModel->getPk();
                $menu_exp = strpos($search_words, ',') ? 'in' : '=';
                $menu_where[] = [$search_field, $menu_exp, $search_words];
                $admin_menu_ids = $MenuModel->where($menu_where)->column($MenuPk);
                $where[] = [$MenuPk, 'in', $admin_menu_ids];
            }
            else {
                $where[] = [$search_field, 'like', '%' . $search_words . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = UserLogService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 用户日志信息
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['admin_user_log_id'] = input('get.admin_user_log_id/d', 0);

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
