<?php
/**
 * Description: 内容管理验证器
 * File: ContentValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\cms;

use think\Validate;

class ContentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'content_id' => ['require'],
        'category_id' => ['require'],
        'name' => ['require'],
        'image' => ['array'],
        'video' => ['array'],
        'file' => ['array'],
        'is_top' => ['require', 'in' => '0,1'],
        'is_hot' => ['require', 'in' => '0,1'],
        'is_rec' => ['require', 'in' => '0,1'],
        'is_hide' => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'category_id.require' => '请选择分类',
        'name.require' => '请输入名称',
        'is_top.in' => '是否置顶，1是0否',
        'is_hot.in' => '是否热门，1是0否',
        'is_rec.in' => '是否推荐，1是0否',
        'is_hide.in' => '是否隐藏，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info' => ['content_id'],
        'add' => ['category_id', 'name', 'image', 'video', 'file'],
        'edit' => ['content_id', 'category_id', 'name', 'image', 'video', 'file'],
        'dele' => ['ids'],
        'cate' => ['ids', 'category_id'],
        'istop' => ['ids', 'is_top'],
        'ishot' => ['ids', 'is_hot'],
        'isrec' => ['ids', 'is_rec'],
        'ishide' => ['ids', 'is_hide'],
        'reco' => ['ids'],
    ];
}
