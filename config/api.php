<?php
/**
 * Description: api配置
 * File: api.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

return [
    // 无需登录接口url
    'api_is_unlogin' => [
        'api/',
        'api/Index/index',
        'api/Register/captcha',
        'api/Register/register',
        'api/Login/captcha',
        'api/Login/login'
    ],
    // 无需限率接口url
    'api_is_unrate' => [],
    // token名称，必须与前端设置一致
    'token_name' => env('token.api_token_name', 'ApiToken')
];
