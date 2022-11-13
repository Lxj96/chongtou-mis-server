<?php
/**
 * Description: 概况信息
 * File: System.php
 * User: Lxj
 * DateTime: 2022-11-13 00:16
 */

namespace app\admin\controller\admin;

use app\common\model\admin\SystemModel;
use app\common\service\admin\SystemService;
use app\common\validate\admin\SystemValidate;

class System
{
    /**
     * 概况信息
     */
    public function index()
    {
        $data = SystemService::info();
        return success($data);
    }

    public function update()
    {
        $param['id'] = input('id/d', 0);
        $param['content'] = input('content/s', '');
        $param['files'] = input('files/a', []);
        validate(SystemValidate::class)->scene('edit')->check($param);

        $data = SystemService::edit($param);

        return success($data);
    }
}