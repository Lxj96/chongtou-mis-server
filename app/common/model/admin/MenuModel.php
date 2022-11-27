<?php
/**
 * Description: 菜单管理模型
 * File: MenuModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class MenuModel extends BaseModel
{
    // 表名
    protected $name = 'admin_menu';
    protected $pk = 'menu_id';
    protected $type = [
        'is_disable' => 'boolean',
        'is_unauth' => 'boolean',
        'is_unlogin' => 'boolean',
        'is_unlog' => 'boolean',
    ];
}
