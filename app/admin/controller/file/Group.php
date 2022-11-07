<?php
/**
 * Description: 文件分组控制器
 * File: Group.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\file;

use app\common\exception\MissException;
use app\common\service\file\GroupService;
use app\common\validate\file\GroupValidate;
use think\response\Json;

/**
 * @Apidoc\Title("文件分组")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("420")
 */
class Group
{
    /**
     * 文件分组列表
     * @return Json
     */
    public function index()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 构建查询条件
        $where = [];

        $data = GroupService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 文件分组信息
     *
     * @param integer $id 文件分组ID
     *
     * @return Json
     * @throws MissException
     */
    public function read($id)
    {
        $param['group_id'] = $id;

        validate(GroupValidate::class)->scene('info')->check($param);

        $data = GroupService::info($param['group_id']);

        if (empty($data)) {
            throw new MissException();
        }

        return success($data);
    }

    /**
     * 文件分组添加
     *
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function save()
    {
        $param['group_name'] = input('group_name/s', '');
        $param['group_desc'] = input('group_desc/s', '');
        $param['group_sort'] = input('group_sort/d', 250);

        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * 文件分组修改
     * @param integer $id
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function update($id)
    {
        $param['group_id'] = $id;
        $param['group_name'] = input('group_name/s', '');
        $param['group_desc'] = input('group_desc/s', '');
        $param['group_sort'] = input('group_sort/d', 250);

        validate(GroupValidate::class)->scene('edit')->check($param);

        $data = GroupService::edit($param);

        return success($data);
    }

    /**
     * 文件分组删除
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(GroupValidate::class)->scene('del')->check($param);

        $data = GroupService::del($param['ids']);

        return success($data);
    }

    /**
     * 文件分组是否禁用
     * @return Json
     * @throws \app\common\exception\SaveErrorMessage
     */
    public function disable()
    {
        $param['ids'] = input('ids/a', []);
        $param['is_disable'] = input('is_disable/b', false);

        validate(GroupValidate::class)->scene('disable')->check($param);

        $data = GroupService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }
}
