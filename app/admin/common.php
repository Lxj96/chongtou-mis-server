<?php
/**
 * Description: admin公共函数文件
 * File: common.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

use think\facade\Config;
use think\facade\Request;
use app\common\service\admin\MenuService;
use app\common\service\admin\SettingService;
use app\common\service\admin\TokenService;

/**
 * 菜单是否存在
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_exist($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $url_list = MenuService::urlList();

    return in_array($menu_url, $url_list);
}

/**
 * 菜单是否已禁用
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_disable($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $menu = MenuService::info($menu_url);

    return $menu['is_disable'] == 1;
}

/**
 * 菜单是否无需权限
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unauth($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unauth_url = MenuService::unauthUrl();

    return in_array($menu_url, $unauth_url);
}

/**
 * 菜单是否无需登录
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unlogin($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unlogin_url = MenuService::unloginUrl();

    if (in_array($menu_url, $unlogin_url)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否无需限率
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unrate($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unrate_url = MenuService::unrateUrl();
    if (in_array($menu_url, $unrate_url)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否无需日志记录
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unlog($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unlog_url = MenuService::unlogUrl();

    if (in_array($menu_url, $unlog_url)) {
        return true;
    }

    return false;
}

/**
 * 用户token是否设置
 *
 * @return bool
 */
function token_has()
{
    $setting = SettingService::info();
    $token_name = strtolower($setting['token_name']);
    $header = Request::header();
    if (isset($header[$token_name])) {
        return true;
    }

    return false;
}

/**
 * 用户token获取
 *
 * @return string
 */
function get_token()
{
    $setting = SettingService::info();

    return Request::header($setting['token_name'], '');
}

/**
 * 用户id获取
 *
 * @return int
 */
function user_id()
{
    return TokenService::userId(get_token());
}

/**
 * 用户是否超管
 *
 * @param int $user_id 用户id
 *
 * @return bool
 */
function is_super($user_id = 0)
{
    if (empty($user_id)) {
        return false;
    }

    $super_ids = Config::get('menu.super_ids', []);
    if (empty($super_ids)) {
        return false;
    }
    if (in_array($user_id, $super_ids)) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * 日志记录是否开启
 *
 * @return bool
 */
function log_switch()
{
    $setting = SettingService::info();
    if ($setting['log_switch']) {
        return true;
    }
    else {
        return false;
    }
}
