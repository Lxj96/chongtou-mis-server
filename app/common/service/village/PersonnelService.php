<?php
/**
 * Description: 行政村人员
 * File: PersonnelService.php
 * User: Lxj
 * DateTime: 2022-11-13 12:53
 */

namespace app\common\service\village;


use app\common\cache\village\PersonnelCache;
use app\common\exception\MissException;
use app\common\exception\SaveErrorMessage;
use app\common\model\village\PersonnelModel;
use app\common\service\admin\UserService;

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
            $field = [
                'id',
                'village_id',
                'name',
                'sex',
                'phone',
                'idcard',
                'birthday',
                'nationality',
                'TIMESTAMPDIFF( YEAR, birthday, CURDATE()) as age',
                'is_often',
                'is_alone',
                'is_voter',
                'create_time',
                'update_time',
            ];
        }

        if (empty($order)) {
            $order = ['village_id' => 'asc', 'name' => 'asc', 'id' => 'desc'];
        }

        $total = $model->where($where)->count();

        $pages = ceil($total / $pageSize);

        $list = $model->field($field)->where($where)->page($current)->limit($pageSize)->order($order)->select()->toArray();

        $userInfo = UserService::info(user_id());
        $is_show_idcard = $userInfo['is_show_idcard']; // 是否可查看完整身份证
        foreach ($list as $k => $v) {
            $list[$k]['village_name'] = '';
            if (!empty($v['village_id'])) {
                $village = SystemService::info($v['village_id'], false);
                if ($village) {
                    $list[$k]['village_name'] = $village['village_name'];
                }
            }
            if (!$is_show_idcard) {
                $list[$k]['phone'] = substr_replace($v['phone'], '****', 3, 4);
                $list[$k]['idcard'] = substr_replace($v['idcard'], '****', 6, 10);
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
        $param['sex'] = $param['sex'] == '女' ? 2 : 1;
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
        $param['sex'] = $param['sex'] == '女' ? 2 : 1;

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

    /**
     * 修改供暖方式
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function heating($param)
    {
        $model = new PersonnelModel();

        $id = $param['id'];
        unset($param['id']);

        $param['update_time'] = datetime();

        $res = $model->where('id', $id)->where('is_alone', 1)->whereRaw('TIMESTAMPDIFF( YEAR, birthday, CURDATE()) >= 60')->update($param);

        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        PersonnelCache::del($id);

        $param['id'] = $id;

        return $param;
    }
}