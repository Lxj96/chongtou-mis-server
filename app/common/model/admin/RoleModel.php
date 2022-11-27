<?php
/**
 * Description: 角色管理模型
 * File: RoleModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class RoleModel extends BaseModel
{
    // 表名
    protected $name = 'admin_role';
    protected $pk = 'role_id';
    protected $type = [
        'is_disable' => 'boolean',
    ];
}
