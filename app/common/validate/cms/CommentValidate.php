<?php
/**
 * Description: 留言管理验证器
 * File: CommentValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\cms;

use think\Validate;

class CommentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'comment_id' => ['require'],
        'call' => ['require'],
        'mobile' => ['require', 'mobile'],
        'title' => ['require'],
        'content' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'call.require' => '请输入称呼',
        'mobile.require' => '请输入手机',
        'mobile.mobile' => '请输入正确手机号',
        'title.require' => '请输入标题',
        'content.require' => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'info' => ['comment_id'],
        'add' => ['call', 'mobile', 'title', 'content'],
        'edit' => ['comment_id'],
        'dele' => ['ids'],
        'reco' => ['ids'],
        'isread' => ['ids'],
    ];
}
