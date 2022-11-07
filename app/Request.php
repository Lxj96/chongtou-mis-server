<?php
/**
 * Description: 应用请求对象类
 * File: Request.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app;

class Request extends \think\Request
{
    // 全局过滤规则
    protected $filter = ['trim'];
}
