<?php
/**
 * Description: 应用中间件定义文件
 * File: middleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

return [
    // 接口中间件
    \app\api\middleware\ApiMiddleware::class,
    // 接口Token中间件
    \app\api\middleware\ApiTokenMiddleware::class,
    // 会员日志中间件
    \app\api\middleware\MemberLogMiddleware::class,
    // 接口速率中间件
    \app\api\middleware\ApiRateMiddleware::class,
];
