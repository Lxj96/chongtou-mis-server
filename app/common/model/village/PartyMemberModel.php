<?php
/**
 * Description: 党员信息
 * File: PartyMemberModel.php
 * User: Lxj
 * DateTime: 2022-11-13 17:57
 */

namespace app\common\model\village;


use app\common\model\BaseModel;

class PartyMemberModel extends BaseModel
{
    // 表名
    protected $name = 'village_party_member';

    protected $type = [
        'is_out' => 'boolean'
    ];
}