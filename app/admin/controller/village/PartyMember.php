<?php
/**
 * Description: 党员信息
 * File: PartyMember.php
 * User: Lxj
 * DateTime: 2022-11-13 17:57
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\PartyMemberService;
use app\common\service\village\SystemService;
use app\common\validate\village\PartyMemberValidate;

class PartyMember
{
    /**
     * 行政村
     */
    public function village()
    {
        $data['list'] = SystemService::listCache();

        return success($data);
    }

    /**
     * 列表
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
        $village_id = input('village_id/d', 0);
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        // 构建查询条件
        $where = [];
        if (!empty($village_id)) $where[] = ['village_id', '=', $village_id];
        if (!empty($search_words)) $where[] = ['name', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = PartyMemberService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 信息
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['id'] = input('get.id/d', 0);
        validate(PartyMemberValidate::class)->scene('info')->check($param);

        $data = PartyMemberService::info($param['id']);
        if (empty($data)) {
            throw new MissException();
        }

        return success($data);
    }

    /**
     * 添加
     * @return Json
     */
    public function save()
    {
        $param['village_id'] = input('village_id/d', 0);
        $param['name'] = input('name/s', '');
        $param['join_time'] = input('join_time/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['is_out'] = input('is_out/b', false);
        $param['out_address'] = input('out_address/s', '');
        $param['remark'] = input('remark/s', '');

        validate(PartyMemberValidate::class)->scene('add')->check($param);

        $data = PartyMemberService::add($param);

        return success($data);
    }

    /**
     * 修改
     * @return Json
     */
    public function update()
    {
        $param['id'] = input('id/d', 0);
        $param['village_id'] = input('village_id/d', 0);
        $param['name'] = input('name/s', '');
        $param['join_time'] = input('join_time/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['is_out'] = input('is_out/b', false);
        $param['out_address'] = input('out_address/s', '');
        $param['remark'] = input('remark/s', '');

        validate(PartyMemberValidate::class)->scene('edit')->check($param);

        $data = PartyMemberService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(PartyMemberValidate::class)->scene('del')->check($param);

        $data = PartyMemberService::del($param['ids']);

        return success($data);
    }
}