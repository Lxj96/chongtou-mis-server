<?php
/**
 * Description: 首页控制器
 * File: Index.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\controller;

use app\api\service\IndexService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("首页")
 * @Apidoc\Sort("110")
 * @Apidoc\Group("index")
 */
class Index
{
    /**
     * @Apidoc\Title("首页")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg = '后端安装成功，欢迎使用，如有帮助，敬请Star！';
        echo phpinfo();
//        return success($data, $msg);
    }
}
