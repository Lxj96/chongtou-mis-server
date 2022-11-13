<?php
/**
 * Description:
 * File: FileGroup.php
 * User: Lxj
 * DateTime: 2022-11-13 20:53
 */

namespace app\common\model\village;


use app\common\model\BaseModel;

class FileGroupModel extends BaseModel
{
    // 表名
    protected $name = 'village_file_group';
    // 表主键
    protected $pk = 'group_id';

    protected $type = [
        'is_disable' => 'boolean',
    ];
}