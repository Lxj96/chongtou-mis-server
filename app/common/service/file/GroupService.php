<?php
/**
 * Description: 文件分组
 * File: GroupService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\file;

use app\common\cache\file\GroupCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\file\GroupModel;

class GroupService
{
    /**
     * 文件分组列表
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     */
    public static function list($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',group_name,group_desc,group_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['group_sort' => 'desc', $pk => 'desc'];
        }

        $total = $model->where($where)->count($pk);

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 文件分组信息
     *
     * @param int $id 文件分组id
     *
     * @return array
     * @throws MissException
     */
    public static function info($id)
    {
        $info = GroupCache::get($id);
        if (empty($info)) {
            $model = new GroupModel();
            $info = $model->find($id);
            if (empty($info)) {
                throw new MissException('文件分组不存在：' . $id);

            }
            $info = $info->toArray();

            GroupCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 文件分组添加
     *
     * @param array $param 文件分组信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function add($param)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件分组修改
     *
     * @param array $param 文件分组信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        GroupCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件分组删除
     *
     * @param array $ids 文件分组id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function del($ids)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            GroupCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件分组禁用
     *
     * @param array $ids 文件分组id
     * @param int $is_disable 是否禁用
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function disable($ids, $is_disable = 0)
    {
        $model = new GroupModel();
        $pk = $model->getPk();

        $update['is_disable'] = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            GroupCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
