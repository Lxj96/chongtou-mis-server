<?php
/**
 * Description:
 * User: lxj
 * Date: 2020/6/8
 * Time: 23:04
 */

namespace app\common\validate;


use think\Validate;

class IDValidateInt extends Validate
{
    protected $rule = [
        'id' => 'require|integer',
    ];

    protected $message = [
        'id' => 'ID必需是正整数'
    ];
}