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
use think\facade\Db;
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

        // 检索字段
        $group_id = input('group_id/s', '');
        $is_disable = input('is_disable/b');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        // 构建查询条件
        $where = [];
        if (!empty($search_words)) $where[] = ['group_name|group_desc', 'like', '%' . $search_words . '%'];
        if (!empty($group_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(group_id,'" . $group_id . "')")];

        if (is_bool($is_disable)) {
            $where[] = ['is_disable', '=', $is_disable];
        }

        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = GroupService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 文件分组信息
     *
     * @return Json
     * @throws MissException
     */
    public function read()
    {
        $param['group_id'] = input('get.group_id/d', 0);

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
     */
    public function save()
    {
        $param['group_name'] = input('group_name/s', '');
        $param['group_desc'] = input('group_desc/s', '');
        $param['group_sort'] = input('group_sort/d', 250);
        $param['is_disable'] = input('is_disable/b', false);

        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * 文件分组修改
     * @return Json
     */
    public function update()
    {
        $param['group_id'] = input('group_id/d', 0);
        $param['group_name'] = input('group_name/s', '');
        $param['group_desc'] = input('group_desc/s', '');
        $param['group_sort'] = input('group_sort/d', 250);
        $param['is_disable'] = input('is_disable/b', false);

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
