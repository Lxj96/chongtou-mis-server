<?php
/**
 * Description: 产业分部
 * File: Industry.php
 * User: Lxj
 * DateTime: 2022-11-14 18:42
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\IndustryService;
use app\common\service\village\SystemService;
use app\common\validate\village\IndustryValidate;

class Industry
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
        if (!empty($search_words)) $where[] = ['name|address|owner', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = IndustryService::list($where, $current, $pageSize, $order);

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
        validate(IndustryValidate::class)->scene('info')->check($param);

        $data = IndustryService::info($param['id']);
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
        $param['address'] = input('address/s', '');
        $param['owner'] = input('owner/s', '');
        $param['phone'] = input('phone/s', '');
        $param['room'] = input('room/s', '');
        $param['bed'] = input('bed/s', '');
        $param['dining_table'] = input('dining_table/s', '');
        $param['toilet'] = input('toilet/s', '');
        $param['star'] = input('star/d', 0);
        $param['standard'] = input('standard/s', '');
        $param['license_time'] = input('license_time/s', null);
        $param['license_address'] = input('license_address/s', null);
        $param['remark'] = input('remark/s', '');


        validate(IndustryValidate::class)->scene('add')->check($param);

        $data = IndustryService::add($param);

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
        $param['address'] = input('address/s', '');
        $param['owner'] = input('owner/s', '');
        $param['phone'] = input('phone/s', '');
        $param['room'] = input('room/s', '');
        $param['bed'] = input('bed/s', '');
        $param['dining_table'] = input('dining_table/s', '');
        $param['toilet'] = input('toilet/s', '');
        $param['star'] = input('star/d', 0);
        $param['standard'] = input('standard/s', '');
        $param['license_time'] = input('license_time/s', null);
        $param['license_address'] = input('license_address/s', null);
        $param['remark'] = input('remark/s', '');

        validate(IndustryValidate::class)->scene('edit')->check($param);

        $data = IndustryService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(IndustryValidate::class)->scene('del')->check($param);

        $data = IndustryService::del($param['ids']);

        return success($data);
    }
}