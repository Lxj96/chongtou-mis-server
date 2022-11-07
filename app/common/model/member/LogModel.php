<?php
/**
 * Description: 会员日志模型
 * File: LogModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class LogModel extends Model
{
    // 表名
    protected $name = 'Member_log';
    // 表主键
    protected $pk = 'member_log_id';

    /**
     * @Apidoc\Field("member_log_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("member_log_id,member_id,api_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time")
     */
    public function listReturn()
    {
    }

    /**
     *
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("log_type")
     */
    public function log_type()
    {
    }
}
