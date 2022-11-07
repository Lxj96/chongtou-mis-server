<?php
/**
 * Description: 操作频繁异常处理类
 * File: FrequentException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;


class FrequentException extends BaseException
{
    public $code = 429;
    public $message = '您的操作过于频繁，请稍后再试';
}