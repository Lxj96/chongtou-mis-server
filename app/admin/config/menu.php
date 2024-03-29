<?php
/**
 * Description: menu配置
 * File: menu.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

return [
    // 超管用户ID（所有权限）
    'super_ids' => [1],
    // 无需登录菜单url
    'menu_is_unlogin' => [
        'admin/admin.Login/setting',
        'admin/admin.Login/captcha',
        'admin/admin.Login/login',
    ],
    // 无需权限菜单url
    'menu_is_unauth' => [
        'admin/Index/index',
        'admin/admin.Login/logout',
        'admin/admin.UserCenter/info',
        'admin/file.File/downLog',
    ],
    // 无需限率菜单url
    'menu_is_unrate' => [
        'admin/file.File/add',
        'admin/file.File/list'
    ],
    // 无需日志记录菜单url
    'menu_is_unlog' => [],
    // token名称，必须与前端设置一致
    'token_name' => env('token.admin_token_name', 'AdminToken')
];
