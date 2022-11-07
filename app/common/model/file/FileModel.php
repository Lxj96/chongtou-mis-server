<?php
/**
 * Description: 文件管理模型
 * File: FileModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\file;

use app\common\model\BaseModel;

class FileModel extends BaseModel
{
    // 表名
    protected $name = 'file';
    // 表主键
    protected $pk = 'file_id';

    protected $type = [
        'is_front' => 'boolean',
        'is_disable' => 'boolean',
    ];
}
