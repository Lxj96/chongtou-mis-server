<?php
/**
 * Description: 行政村人员
 * File: Personnel.php
 * User: Lxj
 * DateTime: 2022-11-13 12:52
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\village\PersonnelService;
use app\common\service\village\SystemService;
use app\common\validate\village\PersonnelValidate;
use think\facade\Db;

class Personnel
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
        $sex = input('sex/d', 0);
        $age = input('age/d', 0);
        $is_often = input('is_often/b');
        $is_alone = input('is_alone/b');
        $is_voter = input('is_voter/b');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        // 构建查询条件
        $where = [];
        if (!empty($village_id)) $where[] = ['village_id', '=', $village_id];
        if (!empty($sex)) $where[] = ['sex', '=', $sex];
        if (!empty($age)) {
            if ($age === 1) {
                $where[] = ['', 'exp', Db::raw('TIMESTAMPDIFF( YEAR, birthday, CURDATE()) < 60')];
            }
            else {
                $where[] = ['', 'exp', Db::raw('TIMESTAMPDIFF( YEAR, birthday, CURDATE()) >= 60')];
            }
        }
        if (is_bool($is_often)) $where[] = ['is_often', '=', $is_often];
        if (is_bool($is_alone)) $where[] = ['is_alone', '=', $is_alone];
        if (is_bool($is_voter)) $where[] = ['is_voter', '=', $is_voter];
        if (!empty($search_words)) $where[] = ['name|idcard|nationality', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = PersonnelService::list($where, $current, $pageSize, $order);

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
        validate(PersonnelValidate::class)->scene('info')->check($param);

        $data = PersonnelService::info($param['id']);
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
        $param['idcard'] = input('idcard/s', '');
        $param['sex'] = input('sex/s', '男');
        $param['birthday'] = input('birthday/s', null);
        $param['nationality'] = input('nationality/s', '');
        $param['phone'] = input('phone/s', '');
        $param['patriarch_no'] = input('patriarch_no/s', '');
        $param['patriarch_relation'] = input('patriarch_relation/s', '');
        $param['urban_rural'] = input('urban_rural/s', '');
        $param['state'] = input('state/s', '');
        $param['patriarch_type'] = input('patriarch_type/s', '');
        $param['address'] = input('address/s', '');
        $param['police'] = input('police/s', '');
        $param['committee'] = input('committee/s', '');
        $param['migrate_time'] = input('migrate_time/s', null);
        $param['migrate_reason'] = input('migrate_reason/s', '');
        $param['job'] = input('job/s', '');
        $param['category'] = input('category/s', '');
        $param['come_time'] = input('come_time/s', null);
        $param['come_reason'] = input('come_reason/s', '');
        $param['is_lock'] = input('is_lock/b', true);
        $param['is_freeze'] = input('is_freeze/b', true);
        $param['is_current_address'] = input('is_current_address/b', true);
        $param['is_often'] = input('is_often/b', true);
        $param['is_alone'] = input('is_alone/b', false);
        $param['is_voter'] = input('is_voter/b', false);
        $param['remark'] = input('remark/s', '');

        validate(PersonnelValidate::class)->scene('add')->check($param);

        $data = PersonnelService::add($param);

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
        $param['idcard'] = input('idcard/s', '');
        $param['sex'] = input('sex/s', '男');
        $param['birthday'] = input('birthday/s', null);
        $param['nationality'] = input('nationality/s', '');
        $param['phone'] = input('phone/s', '');
        $param['patriarch_no'] = input('patriarch_no/s', '');
        $param['patriarch_relation'] = input('patriarch_relation/s', '');
        $param['urban_rural'] = input('urban_rural/s', '');
        $param['state'] = input('state/s', '');
        $param['patriarch_type'] = input('patriarch_type/s', '');
        $param['address'] = input('address/s', '');
        $param['police'] = input('police/s', '');
        $param['committee'] = input('committee/s', '');
        $param['migrate_time'] = input('migrate_time/s', null);
        $param['migrate_reason'] = input('migrate_reason/s', '');
        $param['job'] = input('job/s', '');
        $param['category'] = input('category/s', '');
        $param['come_time'] = input('come_time/s', null);
        $param['come_reason'] = input('come_reason/s', '');
        $param['is_lock'] = input('is_lock/b', true);
        $param['is_freeze'] = input('is_freeze/b', true);
        $param['is_current_address'] = input('is_current_address/b', true);
        $param['is_often'] = input('is_often/b', true);
        $param['is_alone'] = input('is_alone/b', false);
        $param['is_voter'] = input('is_voter/b', false);
        $param['remark'] = input('remark/s', '');

        validate(PersonnelValidate::class)->scene('edit')->check($param);

        $data = PersonnelService::edit($param);

        return success($data);
    }

    /**
     * 删除
     * @return Json
     */
    public function delete()
    {
        $param['ids'] = input('ids/a', []);

        validate(PersonnelValidate::class)->scene('del')->check($param);

        $data = PersonnelService::del($param['ids']);

        return success($data);
    }

    /**
     * 修改供暖方式
     * @return Json
     */
    public function heating(){
        $param['id'] = input('id/d', 0);
        $param['heating'] = input('heating/s', '');
        $param['life'] = input('life/s', '');
        $param['remark'] = input('remark/s', '');

        validate(PersonnelValidate::class)->scene('id')->check($param);

        $data = PersonnelService::heating($param);

        return success($data);
    }
}