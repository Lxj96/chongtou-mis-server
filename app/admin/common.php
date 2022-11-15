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
 * 菜单url获取
 * 应用/控制器/操作
 *
 * @return string eg：admin/Index/index
 */
function menu_url()
{
    $url = app('http')->getName() . '/' . Request::pathinfo();
    // 删除最后一个/
    $reg = '/\/$/i';
    $url = preg_replace($reg, '', $url);
    // 自动补index
    if (substr_count($url, '/') == 1) {
        $url = $url . '/index';
    }

//    return app('http')->getName() . '/' . Request::controller() . '/' . Request::action();
    return $url;
}

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
    if (in_array($menu_url, $url_list)) {
        return true;
    }

    return false;
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
    if ($menu['is_disable'] == 1) {
        return true;
    }

    return false;
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
    if (in_array($menu_url, $unauth_url)) {
        return true;
    }

    return false;
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
 * 用户token是否设置
 *
 * @return bool
 */
function admin_token_has()
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
function admin_token()
{
    $setting = SettingService::info();

    return Request::header($setting['token_name'], '');
}

/**
 * 用户id获取
 *
 * @return int
 */
function admin_user_id()
{
    return TokenService::adminUserId(admin_token());
}

/**
 * 用户是否超管
 *
 * @param int $admin_user_id 用户id
 *
 * @return bool
 */
function admin_is_super($admin_user_id = 0)
{
    if (empty($admin_user_id)) {
        return false;
    }

    $admin_super_ids = Config::get('admin.super_ids', []);
    if (empty($admin_super_ids)) {
        return false;
    }
    if (in_array($admin_user_id, $admin_super_ids)) {
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
function admin_log_switch()
{
    $setting = SettingService::info();
    if ($setting['log_switch']) {
        return true;
    }
    else {
        return false;
    }
}
