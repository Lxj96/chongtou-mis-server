<?php
/**
 * Description: 全局中间件定义文件
 * File: middleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

return [
    // 全局跨域请求
    \app\common\middleware\AllowCrossDomain::class,
    // 日志清除
    // \app\common\middleware\LogClear::class,
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    // \think\middleware\SessionInit::class,
];
