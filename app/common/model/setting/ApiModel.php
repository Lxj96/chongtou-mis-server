<?php
/**
 * Description: 接口管理模型
 * File: ApiModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class ApiModel extends Model
{
    // 表名
    protected $name = 'api';
    // 表主键
    protected $pk = 'api_id';

    /**
     * @Apidoc\Field("api_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("api_id,api_pid,api_name,api_url,api_sort,is_disable,is_unlogin,create_time,update_time")
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
     * @Apidoc\Field("api_pid,api_name,api_url,api_sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("api_id,api_pid,api_name,api_url,api_sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("api_pid")
     */
    public function api_pid()
    {
    }

    /**
     * @Apidoc\Field("api_url")
     */
    public function api_url()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }

    /**
     * @Apidoc\Field("is_unlogin")
     */
    public function is_unlogin()
    {
    }
}
