<?php
/**
 * Description: 用户日志模型
 * File: UserLogModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\admin;

use app\common\model\BaseModel;

class UserLogModel extends BaseModel
{
    // 表名
    protected $name = 'admin_user_log';
    // 表主键
    protected $pk = 'admin_user_log_id';
}
