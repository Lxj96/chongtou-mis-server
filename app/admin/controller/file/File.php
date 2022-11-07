<?php
/**
 * Description: 文件管理控制器
 * File: File.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\file;

use app\common\service\file\FileService;
use app\common\service\file\GroupService;
use app\common\service\file\SettingService;
use app\common\validate\file\FileValidate;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

class File
{
    /**
     * 文件分组列表
     * @return Json
     */
    public function group()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $file_name = input('file_name/s', '');
        $file_md5 = input('file_md5/s', '');
        $file_hash = input('file_hash/s', '');
        $file_id = input('file_id/d', 0);

        // 构建查询条件
        $where = [];
        if (!empty($file_id)) $wheres[] = ['', 'exp', Db::raw("FIND_IN_SET('" . $file_id . "',file_id)")];
        if (!empty($file_name)) $where[] = ['file_name', 'like', '%' . $file_name . '%'];
        if (!empty($file_md5)) $where[] = ['file_md5', 'like', '%' . $file_md5 . '%'];
        if (!empty($file_hash)) $where[] = ['file_hash', 'like', '%' . $file_hash . '%'];

        $field = 'group_id,group_name';

        $data = GroupService::list($where, $current, $pageSize, $order, $field);
        $data['filetype'] = SettingService::fileType();
        $data['storage'] = SettingService::storage();

        return success($data);
    }

    /**
     * 文件列表
     * @return Json
     */
    public function index()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $file_name = input('file_name/s', '');
        $file_md5 = input('file_md5/s', '');
        $file_hash = input('file_hash/s', '');
        $file_id = input('file_id/d', 0);
        $group_id = input('group_id/d', 0);
        $file_type = input('file_type/d', 0);
        $is_disable = input('is_disable/b', null);
        $is_front = input('is_front/b', null);
        $storage = input('storage/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($file_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(file_id,'" . $file_id . "')")];
        if (!empty($file_name)) $where[] = ['file_name', 'like', '%' . $file_name . '%'];
        if (!empty($file_md5)) $where[] = ['file_md5', 'like', '%' . $file_md5 . '%'];
        if (!empty($file_hash)) $where[] = ['file_hash', 'like', '%' . $file_hash . '%'];
        if (!empty($group_id)) $where[] = ['group_id', '=', $group_id];
        if (!empty($file_type)) $where[] = ['file_type', '=', $file_type];
        if ($is_disable !== null) $where[] = ['is_disable', '=', $is_disable];
        if ($is_front !== null) $where[] = ['is_front', '=', $is_front];
        if (!empty($storage)) $where[] = ['storage', '=', $storage];

        $data = FileService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件信息")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\infoReturn")
     */
    public function read()
    {
        $param['file_id'] = Request::param('file_id/d', '');

        validate(FileValidate::class)->scene('info')->check($param);

        $data = FileService::info($param['file_id']);
        if ($data['is_delete'] == 1) {
            exception('文件已被删除：' . $param['file_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("文件添加")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\addParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function save()
    {
        $param['file'] = Request::file('file');
        $param['group_id'] = Request::param('group_id/d', 0);
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front'] = Request::param('is_front/s', 0);
        $param['sort'] = Request::param('sort/d', 250);

        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("文件修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\editParam")
     */
    public function update()
    {
        $param['file_id'] = Request::param('file_id/d', '');
        $param['group_id'] = Request::param('group_id/d', 0);
        $param['domain'] = Request::param('domain/s', '');
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front'] = Request::param('is_front/s', 0);
        $param['sort'] = Request::param('sort/d', 250);

        validate(FileValidate::class)->scene('edit')->check($param);

        $data = FileService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function delete()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改分组")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\group_id")
     */
    public function editgroup()
    {
        $param['ids'] = Request::param('ids/a', '');
        $param['group_id'] = Request::param('group_id/d', 0);

        validate(FileValidate::class)->scene('editgroup')->check($param);

        $data = FileService::editgroup($param['ids'], $param['group_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改类型")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_type")
     */
    public function edittype()
    {
        $param['ids'] = Request::param('ids/a', '');
        $param['file_type'] = Request::param('file_type/s', 'image');

        validate(FileValidate::class)->scene('edittype')->check($param);

        $data = FileService::edittype($param['ids'], $param['file_type']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改域名")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\domain")
     */
    public function editdomain()
    {
        $param['ids'] = Request::param('ids/a', '');
        $param['domain'] = Request::param('domain/s', 'image');

        validate(FileValidate::class)->scene('editdomain')->check($param);

        $data = FileService::editdomain($param['ids'], $param['domain']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\is_disable")
     */
    public function disable()
    {
        $param['ids'] = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(FileValidate::class)->scene('disable')->check($param);

        $data = FileService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\listParam")
     * @Apidoc\Param("group_id", require=false, default="")
     * @Apidoc\Param("file_type", require=false, default="")
     * @Apidoc\Param("is_disable", require=false, default="")
     * @Apidoc\Param("is_front", require=false, default="0")
     * @Apidoc\Param("storage", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表",
     *     @Apidoc\Returned(ref="app\common\model\file\FileModel\listReturn")
     * )
     */
    public function recover()
    {
        $page = Request::param('page/d', 1);
        $limit = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_value = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field = Request::param('date_field/s', '');
        $date_value = Request::param('date_value/a', '');
        $group_id = Request::param('group_id/s', '');
        $file_type = Request::param('file_type/s', '');
        $is_disable = Request::param('is_disable/s', '');
        $is_front = Request::param('is_front/s', '');
        $storage = Request::param('storage/s', '');

        if ($search_field && $search_value) {
            if ($search_field == 'file_id') {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            }
            else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 1];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if ($group_id) {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_disable != '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        if ($is_front != '') {
            $where[] = ['is_front', '=', $is_front];
        }
        if ($storage != '') {
            $where[] = ['storage', '=', $storage];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = FileService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('reco')->check($param);

        $data = FileService::recoverReco($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::recoverDele($param['ids']);

        return success($data);
    }
}
