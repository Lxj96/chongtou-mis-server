<?php
/**
 * Description: 行政村概况信息
 * File: System.php
 * User: Lxj
 * DateTime: 2022-11-13 09:34
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\SystemService;
use app\common\validate\village\SystemValidate;

class System
{
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
        $search_words = input('search_words/s', '');
        // 构建查询条件
        $where = [];
        if (!empty($search_words)) $where[] = ['village_name', 'like', '%' . $search_words . '%'];

        $data = SystemService::list($where, $current, $pageSize, $order);

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
        validate(SystemValidate::class)->scene('info')->check($param);

        $data = SystemService::info($param['id']);
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
        $param['village_name'] = input('village_name/s', '');
        $param['content'] = input('content/s', '');
        $param['sort'] = input('sort/d', 250);
        $param['imgs'] = input('imgs/a', []);

        validate(SystemValidate::class)->scene('add')->check($param);

        $data = SystemService::add($param);

        return success($data);
    }

    /**
     * 修改
     * @return Json
     */
    public function update()
    {
        $param['id'] = input('id/d', 0);
        $param['village_name'] = input('village_name/s', '');
        $param['content'] = input('content/s', '');
        $param['sort'] = input('sort/d', 250);
        $param['imgs'] = input('imgs/a', []);

        validate(SystemValidate::class)->scene('edit')->check($param);

        $data = SystemService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(SystemValidate::class)->scene('del')->check($param);

        $data = SystemService::del($param['ids']);

        return success($data);
    }

}