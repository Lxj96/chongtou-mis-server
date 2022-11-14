<?php
/**
 * Description: 产业分部
 * File: IndustryService.php
 * User: Lxj
 * DateTime: 2022-11-14 19:45
 */

namespace app\common\service\village;


use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\village\IndustryModel;

class IndustryService
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
        $model = new IndustryModel();

        if (empty($field)) {
            $field = 'id,village_id,name,address,owner,phone,star,license_time,remark,create_time,update_time';
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
        $model = new IndustryModel();
        $info = $model->find($id);
        if (empty($info)) {
            throw new MissException();
        }
        $info = $info->toArray();

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
        $model = new IndustryModel();

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
        $model = new IndustryModel();

        $id = $param['id'];
        unset($param['id']);

        $param['update_time'] = datetime();

        $res = $model->where('id', $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

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
        $model = new IndustryModel();

        $update['delete_time'] = datetime();

        $res = $model->where('id', 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $update['ids'] = $ids;

        return $update;
    }
}