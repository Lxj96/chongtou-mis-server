<?php
/**
 * Description: 设置控制器
 * File: Setting.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\controller\cms;

use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("630")
 * @Apidoc\Group("cms")
 */
class Setting
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="app\common\model\cms\SettingModel\infoReturn")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }
}
