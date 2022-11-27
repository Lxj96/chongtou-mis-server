<?php
/**
 * Description: 个人中心控制器
 * File: UserCenter.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\model\admin\MenuModel;
use app\common\service\admin\UserCenterService;
use app\common\validate\admin\UserCenterValidate;
use think\response\Json;

class UserCenter
{
    /**
     * 我的信息
     * @return  Json
     */
    public function index()
    {
        $param['user_id'] = user_id();

        validate(UserCenterValidate::class)->scene('info')->check($param);

        $data = UserCenterService::info($param['user_id']);

        return success($data);
    }

    /**
     * 修改信息
     * @return  Json
     */
    public function update()
    {
        $param['user_id'] = user_id();
        $param['avatar_id'] = input('avatar_id/d', 0);
        $param['username'] = input('username/s', '');
        $param['nickname'] = input('nickname/s', '');
        $param['phone'] = input('phone/s', '');
        $param['email'] = input('email/s', '');

        validate(UserCenterValidate::class)->scene('edit')->check($param);

        $data = UserCenterService::edit($param);

        return success($data);
    }

    /**
     * 修改密码
     * @return Json
     */
    public function pwd()
    {
        $param['user_id'] = user_id();
        $param['password_old'] = input('password_old/s', '');
        $param['password_new'] = input('password_new/s', '');

        validate(UserCenterValidate::class)->scene('pwd')->check($param);

        $data = UserCenterService::pwd($param);

        return success($data);
    }

    /**
     * 我的日志
     * @return Json
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function log()
    {
        $admin_user_id = user_id();
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $search_field = input('search_field/s', '');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        $log_type = input('log_type/d', '');

        validate(UserCenterValidate::class)->scene('log')->check(['user_id' => $admin_user_id]);

        // 构建查询条件
        $where[] = ['user_id', '=', $admin_user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_words) {
            if (in_array($search_field, ['admin_user_log_id'])) {
                $search_exp = strpos($search_words, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_words];
            }
            elseif (in_array($search_field, ['menu_url', 'menu_name'])) {
                $MenuModel = new MenuModel();
                $MenuPk = $MenuModel->getPk();
                $menu_exp = strpos($search_words, ',') ? 'in' : '=';
                $menu_where[] = [$search_field, $menu_exp, $search_words];
                $admin_menu_ids = $MenuModel->where($menu_where)->column($MenuPk);
                $where[] = [$MenuPk, 'in', $admin_menu_ids];
            }
            else {
                $where[] = [$search_field, 'like', '%' . $search_words . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $data = UserCenterService::log($where, $current, $pageSize, $order);

        return success($data);
    }
}
