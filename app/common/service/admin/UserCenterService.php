<?php
/**
 * Description: 个人中心
 * File: UserCenterService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\cache\admin\UserCache;
use app\common\exception\SaveErrorMessage;
use app\common\model\admin\UserModel;

class UserCenterService
{
    /**
     * 我的信息
     *
     * @param int $admin_user_id 用户id
     *
     * @return array
     */
    public static function info($admin_user_id)
    {
        $data = UserService::info($admin_user_id);

        unset($data['password'], $data['admin_token'], $data['admin_menu_ids'], $data['admin_role_ids'], $data['menu_ids']);

        return $data;
    }

    /**
     * 修改信息
     *
     * @param array $param 用户信息
     *
     * @return array
     * @throws SaveErrorMessage
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function edit($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $admin_user_id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $admin_user_id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        UserCache::upd($admin_user_id);

        return self::info($admin_user_id);
    }

    /**
     * 修改密码
     *
     * @param array $param 用户密码
     *
     * @return array
     * @throws SaveErrorMessage
     * @throws \app\common\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function pwd($param)
    {
        $model = new UserModel();
        $pk = $model->getPk();

        $admin_user_id = $param[$pk];
        $password_old = $param['password_old'];
        $password_new = $param['password_new'];

        $user = UserService::info($admin_user_id);
        if (md5($password_old) != $user['password']) {
            throw new SaveErrorMessage('旧密码错误');
        }

        $update['password'] = md5($password_new);
        $update['update_time'] = datetime();

        $res = $model->where($pk, $admin_user_id)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        UserCache::upd($admin_user_id);

        $update[$pk] = $admin_user_id;

        return $update;
    }

    /**
     * 我的日志
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     *
     * @return array
     * @throws \app\common\exception\AuthException
     * @throws \app\common\exception\MissException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function log($where = [], $current = 1, $pageSize = 10, $order = [], $field = '')
    {
        return UserLogService::list($where, $current, $pageSize, $order, $field);
    }
}
