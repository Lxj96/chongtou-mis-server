<?php
/**
 * Description: 微信控制器
 * File: Wechat.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\controller;

use app\common\service\setting\WechatService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信")
 * @Apidoc\Sort("410")
 * @Apidoc\Group("wechat")
 */
class Wechat
{
    /**
     * @Apidoc\Title("微信公众号接入")
     */
    public function access()
    {
        $app = WechatService::offi();

        $app->server->push(function ($message) {
            return "您好！欢迎使用 yylAdmin !" . $message;
        });

        $response = $app->server->serve();

        $response->send();

        exit;
    }
}
