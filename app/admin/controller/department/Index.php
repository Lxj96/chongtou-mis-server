<?php
/**
 * Description: 科室信息
 * File: Index.php
 * User: Lxj
 * DateTime: 2022-11-15 10:57
 */

namespace app\admin\controller\department;


use app\common\exception\MissException;
use app\common\service\department\DepartmentService;
use app\common\validate\department\DepartmentValidate;

class Index
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
        if (!empty($search_words)) $where[] = ['name', 'like', '%' . $search_words . '%'];

        $data = DepartmentService::list($where, $current, $pageSize, $order);

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
        validate(DepartmentValidate::class)->scene('info')->check($param);

        $data = DepartmentService::info($param['id']);
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
        $param['name'] = input('name/s', '');
        $param['content'] = input('content/s', '');
        $param['sort'] = input('sort/d', 250);
        $param['remark'] = input('remark/s', '');

        validate(DepartmentValidate::class)->scene('add')->check($param);

        $data = DepartmentService::add($param);

        return success($data);
    }

    /**
     * 修改
     * @return Json
     */
    public function update()
    {
        $param['id'] = input('id/d', 0);
        $param['name'] = input('name/s', '');
        $param['content'] = input('content/s', '');
        $param['sort'] = input('sort/d', 250);
        $param['remark'] = input('remark/s', '');


        validate(DepartmentValidate::class)->scene('edit')->check($param);

        $data = DepartmentService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(DepartmentValidate::class)->scene('del')->check($param);

        $data = DepartmentService::del($param['ids']);

        return success($data);
    }
}