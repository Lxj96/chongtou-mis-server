<?php
/**
 * Description: 村域项目
 * File: ProjectService.php
 * User: Lxj
 * DateTime: 2022-11-14 19:05
 */

namespace app\common\service\village;


use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\village\ProjectModel;
use app\common\service\file\FileService;

class ProjectService
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
        $model = new ProjectModel();

        if (empty($field)) {
            $field = 'id,village_id,name,start_time,end_time,liaison,phone,address,construction,scale,content,status,remark,file_id,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['village_id' => 'asc', 'name' => 'asc', 'id' => 'desc'];
        }

        $total = $model->where($where)->count();

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['village_name'] = '';
            if (!empty($v['village_id'])) {
                $village = SystemService::info($v['village_id'], false);
                if ($village) {
                    $list[$k]['village_name'] = $village['village_name'];
                }
            }
            $list[$k]['file'] = FileService::info($v['file_id']);

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
        $model = new ProjectModel();
        $info = $model->find($id);
        if (empty($info)) {
            throw new MissException();
        }

        $info = $info->toArray();
        if (!empty($info['file_id'])) {
            $info['file'] = FileService::info($info['file_id']);
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
        $model = new ProjectModel();

        $param['create_time'] = datetime();

        $id = $model->save($param);
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
        $model = new ProjectModel();

        $id = $param['id'];
        unset($param['id']);

        $info = $model->find($id);
        if (!$info) {
            throw new MissException();
        }

        $param['update_time'] = datetime();

        try {
            $info->save($param);
        } catch (\Exception $e) {
            throw new SaveErrorMessage();
        }

        $param['id'] = $id;

        return $info->toArray();
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
        $model = new ProjectModel();

        $update['delete_time'] = datetime();

        $res = $model->where('id', 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $update['ids'] = $ids;

        return $update;
    }
}