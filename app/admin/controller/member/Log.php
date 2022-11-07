<?php
/**
 * Description: 会员日志控制器
 * File: Log.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\member;

use think\facade\Request;
use app\common\validate\member\LogValidate;
use app\common\service\member\LogService;
use app\common\model\member\MemberModel;
use app\common\model\setting\ApiModel;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员日志")
 * @Apidoc\Group("adminMember")
 * @Apidoc\Sort("220")
 */
class Log
{
    /**
     * @Apidoc\Title("会员日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\log_type")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="会员日志列表",
     *     @Apidoc\Returned(ref="app\common\model\member\LogModel\listReturn")
     * )
     */
    public function list()
    {
        $page = Request::param('page/d', 1);
        $limit = Request::param('limit/d', 10);
        $log_type = Request::param('log_type/d', '');
        $sort_field = Request::param('sort_field/s', '');
        $sort_value = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field = Request::param('date_field/s', 'create_time');
        $date_value = Request::param('date_value/a', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_value) {
            if (in_array($search_field, ['member_id', 'username'])) {
                $MemberModel = new MemberModel();
                $MemberPk = $MemberModel->getPk();
                $member_exp = strpos($search_value, ',') ? 'in' : '=';
                $member_where[] = [$search_field, $member_exp, $search_value];
                $member_ids = $MemberModel->where($member_where)->column($MemberPk);
                $where[] = [$MemberPk, 'in', $member_ids];
            }
            elseif (in_array($search_field, ['api_url', 'api_name'])) {
                $ApiModel = new ApiModel();
                $ApiPk = $ApiModel->getPk();
                $api_exp = strpos($search_value, ',') ? 'in' : '=';
                $api_where[] = [$search_field, $api_exp, $search_value];
                $api_ids = $ApiModel->where($api_where)->column($ApiPk);
                $where[] = [$ApiPk, 'in', $api_ids];
            }
            else {
                $where[] = [$search_field, '=', $search_value];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = LogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志信息")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\id")
     * @Apidoc\Returned(ref="app\common\model\member\LogModel\infoReturn")
     */
    public function info()
    {
        $param['member_log_id'] = Request::param('member_log_id/d', '');

        validate(LogValidate::class)->scene('info')->check($param);

        $data = LogService::info($param['member_log_id']);
        if ($data['is_delete'] == 1) {
            exception('会员日志已被删除：' . $param['member_log_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(LogValidate::class)->scene('dele')->check($param);

        $data = LogService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\id")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\id")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\api_url")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param("clean", type="int", default="0", desc="是否清空所有,1是0否")
     */
    public function clear()
    {
        $member_id = Request::param('member_id/s', '');
        $username = Request::param('username/s', '');
        $api_id = Request::param('api_id/s', '');
        $api_url = Request::param('api_url/s', '');
        $date_value = Request::param('date_value/a', '');
        $clean = Request::param('clean/d', 0);

        $where = [];
        $member_ids = [];
        if ($member_id) {
            $member_ids = array_merge(explode(',', $member_id), $member_ids);
        }
        if ($username) {
            $MemberModel = new MemberModel();
            $MemberPk = $MemberModel->getPk();
            $member_exp = strstr($username, ',') ? 'in' : '=';
            $memberids = $MemberModel->where('username', $member_exp, $username)->column($MemberPk);
            if ($memberids) {
                $member_ids = array_merge($memberids, $member_ids);
            }
        }
        if ($member_ids) {
            $where[] = ['member_id', 'in', $member_ids];
        }

        $api_ids = [];
        if ($api_id) {
            $api_ids = array_merge(explode(',', $api_id), $api_ids);
        }
        if ($api_url) {
            $ApiModel = new ApiModel();
            $ApiPk = $ApiModel->getPk();
            $api_exp = strstr($api_url, ',') ? 'in' : '=';
            $apiids = $ApiModel->where('api_url', $api_exp, $api_url)->column($ApiPk);
            if ($apiids) {
                $api_ids = array_merge($apiids, $api_ids);
            }
        }
        if ($api_ids) {
            $where[] = ['api_id', 'in', $api_ids];
        }

        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = LogService::clear($where, $clean);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志统计")
     * @Apidoc\Param("type", type="string", default="", desc="类型：field字段，date日期")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param("field", type="string", default="", desc="统计字段")
     */
    public function stat()
    {
        $type = Request::param('type/s', '');
        $date = Request::param('date/a', []);
        $field = Request::param('field/s', 'member');

        $data = [];
        $dates = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'num') {
            $num = [];
            foreach ($dates as $v) {
                $num[$v] = LogService::statNum($v);
            }
            $data['num'] = $num;
        }
        elseif ($type == 'date') {
            $data['date'] = LogService::statDate($date);
        }
        elseif ($type == 'field') {
            $data['field'] = LogService::statField($date, $field);
        }
        else {
            $num = [];
            foreach ($dates as $v) {
                $num[$v] = LogService::statNum($v);
            }
            $data['num'] = $num;
            $data['date'] = LogService::statDate($date);
            $data['field'] = LogService::statField($date, $field);
        }

        return success($data);
    }
}
