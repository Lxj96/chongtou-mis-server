<?php
/**
 * Description: 权限校验异常类
 * File: ForbiddenException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $message = '权限不够';
}