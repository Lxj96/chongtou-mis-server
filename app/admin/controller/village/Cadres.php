<?php
/**
 * Description:
 * File: Cadres.php
 * User: Lxj
 * DateTime: 2022-11-13 10:52
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\CadresService;
use app\common\service\village\SystemService;
use app\common\validate\village\CadresValidate;

class Cadres
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
        // 构建查询条件
        $where = [];
        if (!empty($village_id)) $where[] = ['village_id', '=', $village_id];
        if (!empty($search_words)) $where[] = ['organization|name|duties', 'like', '%' . $search_words . '%'];

        $data = CadresService::list($where, $current, $pageSize, $order);

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
        validate(CadresValidate::class)->scene('info')->check($param);

        $data = CadresService::info($param['id']);
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
        $param['organization'] = input('organization/s', '');
        $param['name'] = input('name/s', '');
        $param['duties'] = input('duties/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['remark'] = input('remark/s', '');

        validate(CadresValidate::class)->scene('add')->check($param);

        $data = CadresService::add($param);

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
        $param['organization'] = input('organization/s', '');
        $param['name'] = input('name/s', '');
        $param['duties'] = input('duties/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['remark'] = input('remark/s', '');

        validate(CadresValidate::class)->scene('edit')->check($param);

        $data = CadresService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(CadresValidate::class)->scene('del')->check($param);

        $data = CadresService::del($param['ids']);

        return success($data);
    }
}