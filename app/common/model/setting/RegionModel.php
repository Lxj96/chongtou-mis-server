<?php
/**
 * Description: 地区管理模型
 * File: RegionModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class RegionModel extends Model
{
    // 表名
    protected $name = 'region';
    // 表主键
    protected $pk = 'region_id';

    /**
     * @Apidoc\Field("region_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\WithoutField("region_level,is_delete,create_time,update_time,delete_time")
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
     * @Apidoc\WithoutField("region_id,is_delete,create_time,update_time,delete_time")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\WithoutField("is_delete,create_time,update_time,delete_time")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("region_pid")
     */
    public function region_pid()
    {
    }

    /**
     * @Apidoc\Field("region_name")
     */
    public function region_name()
    {
    }

    /**
     * @Apidoc\Field("region_pinyin")
     */
    public function region_pinyin()
    {
    }
}
