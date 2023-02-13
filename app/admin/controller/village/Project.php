<?php
/**
 * Description: 村域项目
 * File: Project.php
 * User: Lxj
 * DateTime: 2022-11-14 18:38
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\ProjectService;
use app\common\service\village\SystemService;
use app\common\validate\village\ProjectValidate;

class Project
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
        $status = input('status/d', 0);
        $search_words = input('search_words/s', '');
        // 构建查询条件
        $where = [];
        if (!empty($village_id)) $where[] = ['village_id', '=', $village_id];
        if (!empty($status)) $where[] = ['status', '=', $status];
        if (!empty($search_words)) $where[] = ['name|address|construction', 'like', '%' . $search_words . '%'];

        $data = ProjectService::list($where, $current, $pageSize, $order);

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
        validate(ProjectValidate::class)->scene('info')->check($param);

        $data = ProjectService::info($param['id']);
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
        $param['start_time'] = input('start_time/s', '');
        $param['end_time'] = input('end_time/s', '');
        $param['liaison'] = input('liaison/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['construction'] = input('construction/s', '');
        $param['scale'] = input('scale/s', '');
        $param['content'] = input('content/s', '');
        $param['status'] = input('status/d', 1);
        $param['file_id'] = input('file_id/d', null);
        $param['remark'] = input('remark/s', '');

        validate(ProjectValidate::class)->scene('add')->check($param);

        $data = ProjectService::add($param);

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
        $param['start_time'] = input('start_time/s', '');
        $param['end_time'] = input('end_time/s', '');
        $param['liaison'] = input('liaison/s', '');
        $param['phone'] = input('phone/s', '');
        $param['address'] = input('address/s', '');
        $param['construction'] = input('construction/s', '');
        $param['scale'] = input('scale/s', '');
        $param['content'] = input('content/s', '');
        $param['status'] = input('status/d', 1);
        $param['file_id'] = input('file_id/d', null);
        $param['remark'] = input('remark/s', '');

        validate(ProjectValidate::class)->scene('edit')->check($param);

        $data = ProjectService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(ProjectValidate::class)->scene('del')->check($param);

        $data = ProjectService::del($param['ids']);

        return success($data);
    }
}