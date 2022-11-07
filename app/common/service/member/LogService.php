<?php
/**
 * Description: 会员日志
 * File: LogService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\member;

use think\facade\Request;
use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\cache\member\LogCache;
use app\common\service\setting\ApiService;
use app\common\model\setting\ApiModel;
use app\common\model\member\LogModel;
use app\common\model\member\MemberModel;

class LogService
{
    /**
     * 会员日志列表
     *
     * @param array $where 条件
     * @param int $page 分页
     * @param int $limit 数量
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new LogModel();
        $pk = $model->getPk();

        $MemberModel = new MemberModel();
        $MemberPk = $MemberModel->getPk();

        $ApiModel = new ApiModel();
        $ApiPk = $ApiModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $MemberPk . ',' . $ApiPk . ',request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            if (isset($v[$MemberPk])) {
                $list[$k]['username'] = '';
                $list[$k]['nickname'] = '';
                $member = MemberService::info($v[$MemberPk], false);
                if ($member) {
                    $list[$k]['username'] = $member['username'];
                    $list[$k]['nickname'] = $member['nickname'];
                }
            }

            if (isset($v[$ApiPk])) {
                $list[$k]['api_name'] = '';
                $list[$k]['api_url'] = '';
                $api = ApiService::info($v[$ApiPk], false);
                if ($api) {
                    $list[$k]['api_name'] = $api['api_name'];
                    $list[$k]['api_url'] = $api['api_url'];
                }
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 会员日志信息
     *
     * @param int $id 会员日志id
     *
     * @return array
     */
    public static function info($id)
    {
        $info = LogCache::get($id);
        if (empty($info)) {
            $model = new LogModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                exception('会员日志不存在：' . $id);
            }
            $info = $info->toArray();
            if ($info['request_param']) {
                $info['request_param'] = unserialize($info['request_param']);
            }

            $info['username'] = '';
            $info['nickname'] = '';
            $MemberModel = new MemberModel();
            $MemberPk = $MemberModel->getPk();
            $member = MemberService::info($info[$MemberPk], false);
            if ($member) {
                $info['username'] = $member['username'];
                $info['nickname'] = $member['nickname'];
            }

            $info['api_name'] = '';
            $info['api_url'] = '';
            $ApiModel = new ApiModel();
            $ApiPk = $ApiModel->getPk();
            $api = ApiService::info($info[$ApiPk], false);
            if ($api) {
                $info['api_name'] = $api['api_name'];
                $info['api_url'] = $api['api_url'];
            }

            LogCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员日志添加
     *
     * @param array $param 会员日志信息
     * @param int $log_type 日志类型1注册2登录3操作4退出
     *
     * @return void
     */
    public static function add($param = [], $log_type = 3)
    {
        // 会员日记是否开启
        if (member_log_switch()) {
            if ($log_type == 1) {
                $param['response_code'] = 200;
                $param['response_msg'] = '注册成功';
            }
            elseif ($log_type == 2) {
                $param['response_code'] = 200;
                $param['response_msg'] = '登录成功';
            }
            elseif ($log_type == 4) {
                $param['response_code'] = 200;
                $param['response_msg'] = '退出成功';
            }

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

            $api = ApiService::info();
            $ip_info = IpInfoUtils::info();

            $param['api_id'] = $api['api_id'];
            $param['log_type'] = $log_type;
            $param['request_ip'] = $ip_info['ip'];
            $param['request_country'] = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city'] = $ip_info['city'];
            $param['request_area'] = $ip_info['area'];
            $param['request_region'] = $ip_info['region'];
            $param['request_isp'] = $ip_info['isp'];
            $param['request_param'] = serialize($request_param);
            $param['request_method'] = Request::method();
            $param['create_time'] = datetime();

            $model = new LogModel();
            $model->strict(false)->insert($param);
        }
    }

    /**
     * 会员日志修改
     *
     * @param array $param 会员日志信息
     *
     * @return array
     */
    public static function edit($param)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        LogCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 会员日志删除
     *
     * @param array $ids 会员日志id
     *
     * @return array
     */
    public static function dele($ids)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        $update['is_delete'] = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            LogCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 会员日志清除
     *
     * @param array $where 清除条件
     * @param int $clean 清空所有
     *
     * @return array
     */
    public static function clear($where = [], $clean = 0)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if ($clean) {
            $count = $model->where($pk, '>', 0)->delete(true);
        }
        else {
            $count = $model->where($where)->delete();
        }

        $data['count'] = $count;
        $data['where'] = $where;

        return $data;
    }

    /**
     * 会员日志数量统计
     *
     * @param string $date 日期
     *
     * @return int
     */
    public static function statNum($date = 'total')
    {
        $key = 'num:' . $date;
        $data = LogCache::get($key);
        if (empty($data)) {
            $model = new LogModel();
            $pk = $model->getPk();

            $where[] = ['is_delete', '=', 0];
            if ($date == 'total') {
                $where[] = [$pk, '>', 0];
            }
            else {
                if ($date == 'yesterday') {
                    $yesterday = DatetimeUtils::yesterday();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($yesterday);
                }
                elseif ($date == 'thisweek') {
                    list($start, $end) = DatetimeUtils::thisWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'lastweek') {
                    list($start, $end) = DatetimeUtils::lastWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'thismonth') {
                    list($start, $end) = DatetimeUtils::thisMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                }
                elseif ($date == 'lastmonth') {
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

            LogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日志日期统计
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
        $data = LogCache::get($key);
        if (empty($data)) {
            $model = new LogModel();

            $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
            $where[] = ['create_time', '>=', DatetimeUtils::dateStartTime($sta_date)];
            $where[] = ['create_time', '<=', DatetimeUtils::dateEndTime($end_date)];
            $group = "date_format(create_time,'%Y-%m-%d')";

            $member_log = $model->field($field)->where($where)->group($group)->select();

            $x_data = DatetimeUtils::betweenDates($sta_date, $end_date);
            $y_data = [];
            foreach ($x_data as $k => $v) {
                $y_data[$k] = 0;
                foreach ($member_log as $vu) {
                    if ($v == $vu['date']) {
                        $y_data[$k] = $vu['num'];
                    }
                }
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date'] = $date;

            LogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日志字段统计
     *
     * @param array $date 日期范围
     * @param string $type 字段类型
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

        if ($type == 'country') {
            $group = 'request_country';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'province') {
            $group = 'request_province';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'isp') {
            $group = 'request_isp';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }
        elseif ($type == 'city') {
            $group = 'request_city';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }
        else {
            $group = 'member_id';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key = 'field:' . 'top' . $top . $type . '-' . $sta_date . '-' . $end_date;
        $data = LogCache::get($key);
        if (empty($data)) {
            $LogModel = new LogModel();
            $LogPk = $LogModel->getPk();

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', DatetimeUtils::dateStartTime($date[0])];
            $where[] = ['create_time', '<=', DatetimeUtils::dateEndTime($date[1])];

            $mlog_field = $field . ', COUNT(' . $LogPk . ') as y_data';
            $member_log = $LogModel->field($mlog_field)->where($where)->group($group)->order('y_data desc')->limit($top)->select()->toArray();

            if ($type == 'member') {
                $member_ids = array_column($member_log, 'x_data');
                $MemberModel = new MemberModel();
                $MemberPk = $MemberModel->getPk();
                $member = $MemberModel->field($MemberPk . ',username')->where($MemberPk, 'in', $member_ids)->select()->toArray();
            }

            $x_data = [];
            $y_data = [];
            $p_data = [];
            foreach ($member_log as $v) {
                if ($type == 'member') {
                    foreach ($member as $vm) {
                        if ($v['x_data'] == $vm['member_id']) {
                            $v['x_data'] = $vm['username'];
                        }
                    }
                }

                $x_data[] = $v['x_data'];
                $y_data[] = $v['y_data'];
                $p_data[] = ['value' => $v['y_data'], 'name' => $v['x_data']];
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['p_data'] = $p_data;
            $data['date'] = $date;

            LogCache::set($key, $data);
        }

        return $data;
    }
}
