<?php
/**
 * Description: 用户日志
 * File: UserLogService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use think\facade\Db;
use think\facade\Request;
use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\cache\admin\UserLogCache;
use app\common\model\admin\UserLogModel;
use app\common\model\admin\UserModel;
use app\common\model\admin\MenuModel;

class UserLogService
{
    /**
     * 用户日志列表
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function list($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $UserPk . ',' . $MenuPk . ',request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }

        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $total = $model->where($where)->count($pk);

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            if (isset($v[$UserPk])) {
                $list[$k]['username'] = '';
                $user = UserService::info($v[$UserPk], false);
                if ($user) {
                    $list[$k]['username'] = $user['username'];
                    $list[$k]['nickname'] = $user['nickname'];
                }
            }

            if (isset($v[$MenuPk])) {
                $list[$k]['menu_name'] = '';
                $list[$k]['menu_url'] = '';
                $menu = MenuService::info($v[$MenuPk], false);
                if ($menu) {
                    $list[$k]['menu_name'] = $menu['menu_name'];
                    $list[$k]['menu_url'] = $menu['menu_url'];
                }
            }
        }

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 用户日志信息
     *
     * @param int $id 用户日志id
     *
     * @return array
     * @throws MissException
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function info($id)
    {
        $info = UserLogCache::get($id);
        if (empty($info)) {
            $model = new UserLogModel();
            $info = $model->find($id);
            if (empty($info)) {
                throw new MissException('用户日志不存在：' . $id);
            }
            $info = $info->toArray();

            if ($info['request_param']) {
                $info['request_param'] = unserialize($info['request_param']);
            }

            $info['username'] = '';
            $info['nickname'] = '';
            $UserModel = new UserModel();
            $UserPk = $UserModel->getPk();
            $user = UserService::info($info[$UserPk], false);
            if ($user) {
                $info['username'] = $user['username'];
                $info['nickname'] = $user['nickname'];
            }

            $info['menu_name'] = '';
            $info['menu_url'] = '';
            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();
            $menu = MenuService::info($info[$MenuPk], false);
            if ($menu) {
                $info['menu_name'] = $menu['menu_name'];
                $info['menu_url'] = $menu['menu_url'];
            }

            UserLogCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 用户日志添加
     *
     * @param array $param 日志数据
     *
     * @return void
     */
    public static function add($param = [])
    {
        // 日志记录是否开启
        if (admin_log_switch()) {
            $request_param = Request::param();
            if (isset($request_param['password'])) {
                unset($request_param['password']);
            }
            if (isset($request_param['new_password'])) {
                unset($request_param['new_password']);
            }
            if (isset($request_param['old_password'])) {
                unset($request_param['old_password']);
            }

            $menu = MenuService::info();
            $ip_info = IpInfoUtils::info();

            $param['admin_menu_id'] = $menu['admin_menu_id'];
            $param['request_ip'] = $ip_info['ip'];
            $param['request_country'] = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city'] = $ip_info['city'];
            $param['request_area'] = $ip_info['area'];
            $param['request_region'] = $ip_info['region'];
            $param['request_isp'] = $ip_info['isp'];
            $param['request_param'] = serialize($request_param);
            $param['request_method'] = Request::method();

            $model = new UserLogModel();
            $model->save($param);
        }
    }

    /**
     * 用户日志修改
     *
     * @param array $param 用户日志
     *
     * @return array
     */
    public static function edit($param = [])
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['request_param'] = serialize($param['request_param']);
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        UserLogCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 用户日志删除
     *
     * @param array $ids 用户日志id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function del($ids)
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            UserLogCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户日志清除
     *
     * @param array $where 清除条件
     * @param bool $clean 清空所有
     *
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function clear($where = [], $clean = false)
    {
        $model = Db::name('admin_user_log');
        if ($clean) {
            $count = $model->delete(true);
        }
        else {
            $count = $model->where($where)->delete();
        }

        $data['count'] = $count;
        $data['where'] = $where;

        return $data;
    }

    /**
     * 用户日志数量统计
     *
     * @param string $date 日期
     *
     * @return int
     */
    public static function statNum($date = 'total')
    {
        $key = 'num:' . $date;
        $data = UserLogCache::get($key);
        if (empty($data) || $data == 0) {
            $model = new UserLogModel();
            $pk = $model->getPk();

            if ($date == 'total') {
                $where[] = [$pk, '>', 0];
            }
            else {
                if ($date == 'yesterday') {
                    $yesterday = DatetimeUtils::yesterday();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($yesterday);
                }
                elseif ($date == 'thisWeek') {
                    list($start, $end) = DatetimeUtils::thisWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'lastWeek') {
                    list($start, $end) = DatetimeUtils::lastWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'thisMonth') {
                    list($start, $end) = DatetimeUtils::thisMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'lastMonth') {
                    list($start, $end) = DatetimeUtils::lastMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                else {
                    $today = DatetimeUtils::today();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($today);
                }

                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
            }

            $data = $model->field($pk)->where($where)->count($pk);

            UserLogCache::set($key, $data);
        }

        return (int)$data;
    }

    /**
     * 用户日志日期统计
     *
     * @param array $date 日期范围
     *
     * @return array
     */
    public static function statDate($date = [])
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key = 'date:' . $sta_date . '-' . $end_date;
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $data = [];
            $model = new UserLogModel();

            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];
            $group = "date_format(create_time,'%Y-%m-%d')";

            $user_log = $model->field($field)->where($where)->group($group)->select()->toArray();

            $dates = DatetimeUtils::betweenDates($sta_date, $end_date);

            foreach ($dates as $k => $v) {
                $value = 0;
                foreach ($user_log as $vul) {
                    if ($v == $vul['date']) {
                        $value = $vul['num'];
                    }
                }
                $data[] = [
                    'date' => $v,
                    'value' => $value,
                ];
            }


            UserLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 用户日志字段统计
     *
     * @param array $date 日期范围
     * @param string $type 统计类型
     * @param int $top top排行
     *
     * @return array
     */
    public static function statField($date = [], $type = 'city', $top = 20)
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key = 'field:' . 'top' . $top . $type . '-' . $sta_date . '-' . $end_date;
        if ($type == 'country') {
            $group = 'request_country';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'province') {
            $group = 'request_province';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'isp') {
            $group = 'request_isp';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'city') {
            $group = 'request_city';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }
        else {
            $group = 'admin_user_id';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }

        $data = UserLogCache::get($key);
        if (empty($data)) {
            $data = [];
            $model = new UserLogModel();
            $pk = $model->getPk();

            $sta_time = DatetimeUtils::dateStartTime($date[0]);
            $end_time = DatetimeUtils::dateEndTime($date[1]);

            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $user_log = $model->field($field . ', COUNT(' . $pk . ') as s')->where($where)->group($group)->order('s desc')->limit($top)->select()->toArray();

            if ($type == 'user') {
                $UserModel = new UserModel();
                $UserPk = $UserModel->getPk();
                $admin_user_ids = array_column($user_log, 'x');
                $user = $UserModel->field($UserPk . ',username')->where($UserPk, 'in', $admin_user_ids)->select()->toArray();
            }

            foreach ($user_log as $v) {
                if ($type == 'user') {
                    foreach ($user as $va) {
                        if ($v['x'] == $va['admin_user_id']) {
                            $v['x'] = $va['username'];
                        }
                    }
                }

                $data[] = ['value' => $v['s'], 'name' => $v['x']];
            }

            UserLogCache::set($key, $data);
        }

        return $data;
    }
}
