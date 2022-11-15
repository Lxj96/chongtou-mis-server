<?php
/**
 * Description: 科室人员
 * File: PersonnelService.php
 * User: Lxj
 * DateTime: 2022-11-15 11:51
 */

namespace app\common\service\department;


use app\common\cache\department\PersonnelCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\department\PersonnelModel;

class PersonnelService
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
        $model = new PersonnelModel();

        if (empty($field)) {
            $field = 'id,department_id,name,duties,phone,state,remark,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['department_id' => 'asc', 'name' => 'asc', 'id' => 'desc'];
        }

        $total = $model->where($where)->count();

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['department_name'] = '';
            if (!empty($v['department_id'])) {
                $department = DepartmentService::info($v['department_id'], false);
                if ($department) {
                    $list[$k]['department_name'] = $department['name'];
                }
            }
        }

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 信息
     *
     * @param int $id
     *
     * @return array
     */
    public static function info($id)
    {
        $info = PersonnelCache::get($id);
        if (empty($info)) {
            $model = new PersonnelModel();
            $info = $model->find($id);
            if (empty($info)) {
                throw new MissException();
            }
            $info = $info->toArray();

            PersonnelCache::set($id, $info);
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
        $model = new PersonnelModel();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }

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
        $model = new PersonnelModel();

        $id = $param['id'];
        unset($param['id']);

        $param['update_time'] = datetime();

        $res = $model->where('id', $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }
        PersonnelCache::del($id);

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
        $model = new PersonnelModel();

        $update['delete_time'] = datetime();

        $res = $model->where('id', 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }
        foreach ($ids as $v) {
            PersonnelCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}