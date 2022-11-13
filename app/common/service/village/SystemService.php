<?php
/**
 * Description:
 * File: SystemService.php
 * User: Lxj
 * DateTime: 2022-11-13 09:59
 */

namespace app\common\service\village;


use app\common\cache\village\SystemCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\village\VillageSystemModel;
use app\common\service\file\FileService;

class SystemService
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
        $model = new VillageSystemModel();

        if (empty($field)) {
            $field = 'id,village_name,content,sort,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'id' => 'desc'];
        }

        $total = $model->where($where)->count();

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        return compact('total', 'pages', 'current', 'pageSize', 'list');
    }

    /**
     * 行政村缓存列表
     */
    public static function listCache()
    {
        $list = SystemCache::get('all');
        if (empty($list)) {
            $model = new VillageSystemModel();

            $list = $model->field('id,village_name')->order(['sort' => 'desc', 'id' => 'desc'])->select()->toArray();

            SystemCache::set('all', $list);
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
     */
    public static function info($id, $exce = true)
    {
        $info = SystemCache::get($id);
        if (empty($info)) {
            $model = new VillageSystemModel();
            $info = $model->find($id);
            if (empty($info) && $exce) {
                throw new MissException();
            }
            $info = $info->toArray();

            $info['imgs'] = FileService::fileArray($info['img_ids']);

            SystemCache::set($id, $info);
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
        $model = new VillageSystemModel();

        $param['create_time'] = datetime();
        $param['img_ids'] = file_ids($param['imgs']);
        unset($param['imgs']);

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }
        SystemCache::del('all');

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
        $model = new VillageSystemModel();

        $id = $param['id'];
        unset($param['id']);

        $param['update_time'] = datetime();
        $param['img_ids'] = file_ids($param['imgs']);
        unset($param['imgs']);

        $res = $model->where('id', $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        SystemCache::del($id);
        SystemCache::del('all');

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
        $model = new VillageSystemModel();

        $update['delete_time'] = datetime();

        $res = $model->where('id', 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            SystemCache::del($v);
        }
        SystemCache::del('all');
        $update['ids'] = $ids;

        return $update;
    }
}