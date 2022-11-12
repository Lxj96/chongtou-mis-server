<?php
/**
 * Description: 控制台控制器
 * File: Index.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\service\IndexService;
use app\common\service\member\MemberService;
use app\common\service\cms\ContentService;
use app\common\service\file\FileService;
use app\common\service\admin\NoticeService;
use hg\apidoc\annotation as Apidoc;

class Index
{
    /**
     * 首页
     */
    public function index()
    {
        $data = IndexService::index();
        $msg = '后端安装成功，欢迎使用，如有帮助，敬请Star！';

        echo 'admin';
        echo phpinfo();
//        return success($data, $msg);
    }

    /**
     * 总数统计
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }
    

    /**
     * 文件统计
     */
    public function file()
    {
        $data = FileService::statistics();

        return success($data);
    }
}
