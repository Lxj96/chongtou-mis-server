<?php
/**
 * Description: 保存、操作失败异常类
 * File: SaveErrorMessage.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;

/**
 * 操作失败
 */
class SaveErrorMessage extends BaseException
{
    public $code = 400;
    public $message = '操作失败';
}