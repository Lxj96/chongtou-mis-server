<?php
/**
 * Description: 容器Provider定义文件
 * File: provider.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

use app\ExceptionHandle;
use app\Request;

return [
    'think\Request' => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
