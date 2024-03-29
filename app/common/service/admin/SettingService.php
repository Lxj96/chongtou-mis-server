<?php
/**
 * Description: 系统设置
 * File: SettingService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\admin;

use app\common\exception\SaveErrorMessage;
use think\facade\Cache;
use think\facade\Config;
use app\common\cache\admin\UserCache;
use app\common\cache\admin\SettingCache;
use app\common\service\file\FileService;
use app\common\model\admin\UserModel;
use app\common\model\admin\SettingModel;
use app\common\utils\EmailUtils;

class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $id = self::$id;
        $info = SettingCache::get($id);

        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk] = $id;
                $info['token_name'] = Config::get('menu.token_name');
                $info['token_key'] = uniqid();
                $model->insert($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();

            $cache_config = Cache::getConfig();
            $info['cache_type'] = $cache_config['default'];

            $info['logo_url'] = '';
            if ($info['logo_id']) {
                $info['logo_url'] = FileService::fileUrl($info['logo_id']);
            }

            $info['favicon_url'] = '';
            if ($info['favicon_id']) {
                $info['favicon_url'] = FileService::fileUrl($info['favicon_id']);
            }
            else {
                $info['favicon_url'] = $info['logo_url'];
            }

            $info['login_bg_url'] = '';
            if ($info['login_bg_id']) {
                $info['login_bg_url'] = FileService::fileUrl($info['login_bg_id']);
            }

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @throws SaveErrorMessage
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $pk = $model->getPk();

        $id = self::$id;

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        SettingCache::del($id);

        return $param;
    }

    /**
     * 缓存设置清除
     *
     * @return array
     */
    public static function cacheClear()
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $user_cache = [];
        $user = $UserModel->field($UserPk)->select()->toArray();
        foreach ($user as $v) {
            $user_old = UserCache::get($v[$UserPk]);
            if ($user_old) {
                $user_cache_temp = [];
                $user_cache_temp[$UserPk] = $user_old[$UserPk];
                $user_cache_temp['token'] = $user_old['token'];
                $user_cache[] = $user_cache_temp;
            }
        }

        $res = Cache::tag('admin')->clear();
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($user_cache as $v) {
            $user_new = UserService::info($v[$UserPk]);
            $user_new['token'] = $v['token'];
            UserCache::set($user_new[$UserPk], $user_new);
        }

        $data['clear'] = $res;

        return $data;
    }

    /**
     * 邮箱设置测试
     *
     * @param array $param
     *
     * @return void
     */
    public static function emailTest($param)
    {
        $setting = self::info();

        $address = $param['email_test'];
        $subject = $setting['system_name'] . '-测试邮件';
        $body = '这是一封测试邮件，收到此邮件说明邮件设置和发送正常。';

        EmailUtils::send($address, $subject, $body);
    }
}
