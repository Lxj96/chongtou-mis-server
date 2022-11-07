<?php
/**
 * Description: 请求失败未能正确获取数据异常类
 * File: MissException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;

/*
 * 数据不存在
 * 通常在查询的数据为空或不存在时返回给客户端
 */

class MissException extends BaseException
{
    public $code = 404;
    public $message = '请求的数据不存在,可能已被限制当前操作';
}