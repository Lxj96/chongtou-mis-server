<?php
/**
 * Description: 设置管理模型
 * File: SettingModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingModel extends Model
{
    // 表名
    protected $name = 'setting';
    // 表主键
    protected $pk = 'setting_id';

    protected $type = [
        'captcha_register' => 'boolean',
        'captcha_login' => 'boolean',
        'log_switch' => 'boolean',
    ];

    /**
     * @Apidoc\Field("token_name,token_key,token_exp")
     */
    public function tokenInfoParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_register,captcha_login")
     */
    public function captchaInfoParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_register")
     */
    public function captchaRegisterParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_login")
     */
    public function captchaLoginParam()
    {
    }

    /**
     * @Apidoc\Field("log_switch")
     */
    public function logInfoParam()
    {
    }

    /**
     * @Apidoc\Field("api_rate_num,api_rate_time")
     */
    public function apiInfoParam()
    {
    }
}
