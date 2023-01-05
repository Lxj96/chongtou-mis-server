<?php
/**
 * Description: 通用文稿目录
 * File: FileGroupModel.php
 * User: Lxj
 * DateTime: 2022-11-15 13:07
 */

namespace app\common\model\currency;


use app\common\model\BaseModel;

class FileGroupModel extends BaseModel
{
    // 表名
    protected $name = 'currency_file_group';
    // 表主键
    protected $pk = 'group_id';

    protected $type = [
        'is_disable' => 'boolean',
    ];
}