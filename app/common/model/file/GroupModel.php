<?php
/**
 * Description: 文件分组模型
 * File: GroupModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\file;

use app\common\model\BaseModel;

class GroupModel extends BaseModel
{
    // 表名
    protected $name = 'file_group';
    // 表主键
    protected $pk = 'group_id';

    protected $type = [
        'is_disable' => 'boolean',
    ];
}
