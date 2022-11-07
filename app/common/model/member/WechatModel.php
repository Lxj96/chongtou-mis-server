<?php
/**
 * Description: 会员微信模型
 * File: WechatModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class WechatModel extends Model
{
    // 表名
    protected $name = 'member_wechat';
    // 表主键
    protected $pk = 'member_wechat_id';

    /**
     * @Apidoc\Field("member_wechat_id")
     */
    public function id()
    {
    }

    /**
     *
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
     * @Apidoc\WithoutField("member_wechat_id")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("member_wechat_id,nickname")
     */
    public function editParam()
    {
    }
}
