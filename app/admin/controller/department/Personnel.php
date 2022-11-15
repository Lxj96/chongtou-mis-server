<?php
/**
 * Description: 科室人员
 * File: Personnel.php
 * User: Lxj
 * DateTime: 2022-11-13 12:52
 */

namespace app\admin\controller\department;


use app\common\exception\MissException;
use app\common\service\department\DepartmentService;
use app\common\service\department\PersonnelService;
use app\common\validate\department\PersonnelValidate;

class Personnel
{
    /**
     * 所属科室
     */
    public function department()
    {
        $data['list'] = DepartmentService::listCache();

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
        $department_id = input('department_id/d', 0);
        $search_words = input('search_words/s', '');
        // 构建查询条件
        $where = [];
        if (!empty($department_id)) $where[] = ['department_id', '=', $department_id];
        if (!empty($search_words)) $where[] = ['name|duties|state', 'like', '%' . $search_words . '%'];

        $data = PersonnelService::list($where, $current, $pageSize, $order);

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
        validate(PersonnelValidate::class)->scene('info')->check($param);

        $data = PersonnelService::info($param['id']);
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
        $param['department_id'] = input('department_id/d', 0);
        $param['name'] = input('name/s', '');
        $param['phone'] = input('phone/s', '');
        $param['duties'] = input('duties/s', '');
        $param['state'] = input('state/s', '');
        $param['remark'] = input('remark/s', '');

        validate(PersonnelValidate::class)->scene('add')->check($param);

        $data = PersonnelService::add($param);

        return success($data);
    }

    /**
     * 修改
     * @return Json
     */
    public function update()
    {
        $param['id'] = input('id/d', 0);
        $param['department_id'] = input('department_id/d', 0);
        $param['name'] = input('name/s', '');
        $param['phone'] = input('phone/s', '');
        $param['duties'] = input('duties/s', '');
        $param['state'] = input('state/s', '');
        $param['remark'] = input('remark/s', '');

        validate(PersonnelValidate::class)->scene('edit')->check($param);

        $data = PersonnelService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(PersonnelValidate::class)->scene('del')->check($param);

        $data = PersonnelService::del($param['ids']);

        return success($data);
    }
}