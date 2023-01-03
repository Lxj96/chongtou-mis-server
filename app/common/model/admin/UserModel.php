<?php
/**
 * Description: 用户管理模型
 * File: UserModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class UserModel extends BaseModel
{
    // 表名
    protected $name = 'admin_user';
    protected $pk = 'user_id';
    protected $type = [
        'is_disable' => 'boolean',
        'is_super' => 'boolean',
        'is_show_idcard' => 'boolean',
    ];
}
