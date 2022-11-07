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

/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("adminConsole")
 * @Apidoc\Sort("150")
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

        echo 'admin';
        echo phpinfo();
//        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("总数统计")
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     */
    public function member()
    {
        $date = Request::param('date/a', []);

        $data = MemberService::statDate($date);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容统计")
     */
    public function cms()
    {
        $data = ContentService::statistics();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件统计")
     */
    public function file()
    {
        $data = FileService::statistics();

        return success($data);
    }

    /**
     * @Apidoc\Title("公告")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="公告列表",
     *     @Apidoc\Returned(ref="app\common\model\admin\NoticeModel\listReturn")
     * )
     */
    public function notice()
    {
        $page = Request::param('page/d', 1);
        $limit = Request::param('limit/d', 10);

        $where[] = ['open_time_start', '<=', datetime()];
        $where[] = ['open_time_end', '>=', datetime()];
        $where[] = ['is_open', '=', 1];
        $where[] = ['is_delete', '=', 0];

        $order = ['sort' => 'desc', 'open_time_start' => 'desc'];

        $data = NoticeService::list($where, $page, $limit, $order);

        return success($data);
    }
}
