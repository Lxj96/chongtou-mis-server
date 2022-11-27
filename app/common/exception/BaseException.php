<?php
/**
 * Description: 自定义异常基类
 * File: BaseException.php
 * User: Lxj
 * DateTime: 2022-03-16 18:05
 */

namespace app\common\exception;

use think\Exception;

/**
 * Class BaseException
 * 自定义异常类的基类
 */
class BaseException extends Exception
{
    public $code = 400; // 错误码
    public $message = 'invalid parameters'; // 向用户显示的消息
    public $showType = 2; // 错误显示类型：0.无声；1.警告；2.错误；4.通知；9.页面

    /**
     * 构造函数，接收一个关联数组
     * @param array|string $params
     */
    public function __construct($params = [])
    {
        if (is_array($params)) {
            if (array_key_exists('code', $params)) {
                $this->code = $params['code'];
            }
            if (array_key_exists('message', $params)) {
                $this->message = is_array($params['message']) ? implode(PHP_EOL, $params['message']) : $params['message'];
            }
            if (array_key_exists('showType', $params)) {
                $this->showType = $params['showType'];
            }
        }
        elseif (is_string($params)) {
            $this->message = $params;
        }
        else {
            return;
        }
    }
}

