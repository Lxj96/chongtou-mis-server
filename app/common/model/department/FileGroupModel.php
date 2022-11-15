<?php
/**
 * Description: 科室文档目录
 * File: FileGroupModel.php
 * User: Lxj
 * DateTime: 2022-11-15 13:07
 */

namespace app\common\model\department;


use app\common\model\BaseModel;

class FileGroupModel extends BaseModel
{
    // 表名
    protected $name = 'department_file_group';
    // 表主键
    protected $pk = 'group_id';

    protected $type = [
        'is_disable' => 'boolean',
    ];
}