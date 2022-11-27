<?php
/**
 * Description: 应用中间件定义文件
 * File: middleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

return [
    // 日志清除
    \app\admin\middleware\LogClear::class,
    // 日志记录中间件
    \app\admin\middleware\UserLogMiddleware::class,
    // Token验证中间件
    \app\admin\middleware\TokenVerifyMiddleware::class,
    // 权限验证中间件
    \app\admin\middleware\AuthVerifyMiddleware::class,
    // 接口速率中间件
    \app\admin\middleware\ApiRateMiddleware::class,
];
