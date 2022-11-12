<?php
/**
 * Description: 登录退出控制器
 * File: Login.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\admin;

use app\common\exception\ParameterException;
use app\common\service\admin\LoginService;
use app\common\service\admin\SettingService;
use app\common\utils\CaptchaUtils;
use app\common\validate\admin\UserValidate;
use think\response\Json;

class Login
{
    /**
     * 设置信息
     *
     * @Method GET
     * @return Json
     */
    public function setting()
    {
        $setting = SettingService::info();
        // 系统设置
        $data['system_name'] = $setting['system_name'];
        $data['title'] = $setting['system_name'];
        $data['page_title'] = $setting['page_title'];
        $data['logo_url'] = $setting['logo_url'];
        $data['favicon_url'] = $setting['favicon_url'];
        $data['login_bg_url'] = $setting['login_bg_url'];

        if ($setting['captcha_switch']) {
            $captcha = CaptchaUtils::create($setting['captcha_type']);
            $data = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data['captcha_switch'] = $setting['captcha_switch'];

        if ($setting['captcha_switch']) {
            $captcha = CaptchaUtils::create($setting['captcha_type']);
            $data = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * 登录
     * @return Json
     * @throws ParameterException
     */
    public function login()
    {
        $param['username'] = input('username/s', '');
        $param['password'] = input('password/s', '');
        $param['captcha_id'] = input('captcha_id/s', '');
        $param['captcha_code'] = input('captcha_code/s', '');

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::info();

        if ($setting['captcha_switch']) {
            if (empty($param['captcha_code'])) {
                throw new ParameterException('请输入验证码');
            }
            $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($captcha_check)) {
                throw new ParameterException('验证码错误');
            }
        }

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * 刷新Token
     *
     *
     * @return json
     */
    public function refresh()
    {
        $param['admin_user_id'] = admin_user_id();
        validate(UserValidate::class)->scene('id')->check($param);

        $data = LoginService::refresh($param['admin_user_id']);

        return success($data, 'Token刷新成功');
    }

    /**
     * 退出登录
     * @return Json
     */
    public function logout()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserValidate::class)->scene('id')->check($param);

        $data = LoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
