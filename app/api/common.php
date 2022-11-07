<?php
/**
 * Description: api公共函数文件
 * File: common.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

use think\facade\Request;
use app\common\service\setting\ApiService;
use app\common\service\setting\TokenService;
use app\common\service\setting\SettingService;

/**
 * 接口url获取
 * 应用/控制器/操作
 * eg：api/Index/index
 *
 * @return string
 */
function api_url()
{
    return app('http')->getName() . '/' . Request::pathinfo();
}

/**
 * 接口是否存在
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_exist($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $url_list = ApiService::urlList();
    if (in_array($api_url, $url_list)) {
        return true;
    }

    return false;
}

/**
 * 接口是否已禁用
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_disable($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $api = ApiService::info($api_url);
    if ($api['is_disable'] == 1) {
        return true;
    }

    return false;
}

/**
 * 接口是否无需登录
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_unlogin($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unlogin_url = ApiService::unloginUrl();
    if (in_array($api_url, $unlogin_url)) {
        return true;
    }

    return false;
}

/**
 * 接口是否无需限率
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_unrate($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unrate_url = ApiService::unrateUrl();
    if (in_array($api_url, $unrate_url)) {
        return true;
    }

    return false;
}

/**
 * 接口token是否设置
 *
 * @return bool
 */
function api_token_has()
{
    $setting = SettingService::info();
    $token_key = $setting['token_name'];
    $token_key = strtolower($token_key);

    $header = Request::header();
    if (isset($header[$token_key])) {
        return true;
    }

    return false;
}

/**
 * 接口token获取
 *
 * @return string
 */
function api_token()
{
    $setting = SettingService::info();
    $token_key = $setting['token_name'];
    $api_token = Request::header($token_key, '');

    return $api_token;
}

/**
 * 接口token验证
 *
 * @param string $api_token 接口token
 *
 * @return Exception
 */
function api_token_verify($api_token = '')
{
    if (empty($api_token)) {
        $api_token = api_token();
    }

    TokenService::verify($api_token);
}

/**
 * 会员id获取
 *
 * @return int
 */
function member_id()
{
    $api_token = api_token();

    return TokenService::memberId($api_token);
}

/**
 * 会员日志是否开启
 *
 * @return bool
 */
function member_log_switch()
{
    $setting = SettingService::info();
    if ($setting['log_switch']) {
        return true;
    }
    else {
        return false;
    }
}
