<?php
/**
 * Description: 公告管理模型
 * File: NoticeModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class NoticeModel extends BaseModel
{
    // 表名
    protected $name = 'admin_notice';
    // 表主键
    protected $pk = 'admin_notice_id';
}
