<?php
/**
 * Description: 身份验证异常类
 * File: AuthException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;


class AuthException extends BaseException
{
    public $code = 401;
    public $message = '身份验证失败，请重新登录。';
}