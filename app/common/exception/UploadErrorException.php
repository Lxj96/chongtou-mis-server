<?php
/**
 * Description: 上传失败异常类
 * File: UploadErrorException.php
 * User: Lxj
 * DateTime: 2022-11-12 19:30
 */

namespace app\common\exception;


class UploadErrorException extends BaseException
{
    public $code = 400;
    public $message = '上传失败';
}