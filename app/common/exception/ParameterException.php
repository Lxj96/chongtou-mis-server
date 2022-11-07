<?php
/**
 * Description:  通用参数类异常错误类
 * File: ParameterException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;

/**
 * Class ParameterException
 * 通用参数类异常错误
 */
class ParameterException extends BaseException
{
    public $code = 422;
    public $message = "请求参数未通过验证";
}