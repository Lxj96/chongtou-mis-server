<?php
/**
 * Description: 科室信息
 * File: IndexService.php
 * User: Lxj
 * DateTime: 2022-11-15 11:17
 */

namespace app\common\service\department;


use app\common\cache\department\DepartmentCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\department\DepartmentModel;

class DepartmentService
{
    /**
     * 列表
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
        $model = new DepartmentModel();

        if (empty($field)) {
            $field = 'id,name,content,sort,remark,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['name' => 'asc', 'id' => 'desc'];
        }

        $total = $model->where($where)->count();

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 科室缓存列表
     */
    public static function listCache()
    {
        $list = DepartmentCache::get('all');
        if (empty($list)) {
            $model = new DepartmentModel();

            $list = $model->field('id,name as department_name')->order(['sort' => 'asc', 'id' => 'desc'])->select()->toArray();

            DepartmentCache::set('all', $list);
        }

        return $list;
    }

    /**
     * 信息
     *
     * @param int $id
     * @param bool $exce 不存在是否抛出异常
     *
     * @return array
     * @throws MissException
     */
    public static function info($id, $exce = true)
    {
        $info = DepartmentCache::get($id);
        if (empty($info)) {
            $model = new DepartmentModel();
            $info = $model->find($id);
            if (empty($info) && $exce) {
                throw new MissException();
            }
            $info = $info->toArray();

            DepartmentCache::set($id, $info);
        }
        return $info;
    }

    /**
     * 添加
     *
     * @param array $param 信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function add($param)
    {
        $model = new DepartmentModel();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }
        DepartmentCache::del('all');

        $param['id'] = $id;

        return $param;
    }

    /**
     * 修改
     *
     * @param array $param 信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {
        $model = new DepartmentModel();

        $id = $param['id'];
        unset($param['id']);

        $param['update_time'] = datetime();

        $res = $model->where('id', $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        DepartmentCache::del($id);
        DepartmentCache::del('all');
        $param['id'] = $id;

        return $param;
    }

    /**
     * 删除
     *
     * @param array $ids id
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function del($ids)
    {
        $model = new DepartmentModel();

        $update['delete_time'] = datetime();

        $res = $model->where('id', 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            DepartmentCache::del($v);
        }
        DepartmentCache::del('all');
        $update['ids'] = $ids;

        return $update;
    }
}