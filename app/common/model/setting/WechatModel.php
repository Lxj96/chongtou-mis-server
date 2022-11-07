<?php
/**
 * Description: 微信设置模型
 * File: WechatModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class WechatModel extends Model
{
    // 表名
    protected $name = 'setting_wechat';
    // 表主键
    protected $pk = 'setting_wechat_id';

    /**
     * @Apidoc\Field("name,origin_id,qrcode,appid,appsecret,url,token,encoding_aes_key,encoding_aes_type")
     */
    public function offiInfoParam()
    {
    }

    /**
     * @Apidoc\Field("name,origin_id,qrcode,appid,appsecret")
     */
    public function miniInfoParam()
    {
    }

    /**
     * @Apidoc\Field("qrcode_url")
     * @Apidoc\AddField("qrcode_url", type="string", default="", desc="二维码链接")
     */
    public function qrcode_url()
    {
    }
}
