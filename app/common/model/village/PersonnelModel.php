<?php
/**
 * Description: 行政村人员
 * File: PersonnelModel.php
 * User: Lxj
 * DateTime: 2022-11-13 13:00
 */

namespace app\common\model\village;


use app\common\model\BaseModel;

class PersonnelModel extends BaseModel
{
    // 表名
    protected $name = 'village_personnel';

    protected $type = [
        'is_lock' => 'boolean',
        'is_freeze' => 'boolean',
        'is_current_address' => 'boolean',
        'is_often' => 'boolean',
        'is_alone' => 'boolean',
        'is_voter' => 'boolean',
    ];

    public static function getSexAttr($value)
    {
        $arr = [1 => '男', 2 => '女'];
        return $arr[$value];
    }
}