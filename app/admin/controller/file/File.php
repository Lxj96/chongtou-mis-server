<?php
/**
 * Description: 文件管理控制器
 * File: File.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\file;

use app\common\exception\MissException;
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
        $file_id = input('file_id/s', '');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        $group_id = input('group_id/d', 0);
        $file_type = input('file_type/s', 0);
        $is_disable = input('is_disable/b');
        $is_front = input('is_front/b');
        $storage = input('storage/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($file_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(file_id,'" . $file_id . "')")];
        if (!empty($search_words)) $where[] = ['file_name|file_md5|file_hash', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if (!empty($group_id)) $where[] = ['group_id', '=', $group_id];
        if (!empty($file_type)) $where[] = ['file_type', '=', $file_type];
        if (is_bool($is_disable)) $where[] = ['is_disable', '=', $is_disable];
        if (is_bool($is_front)) $where[] = ['is_front', '=', $is_front];
        if (!empty($storage)) $where[] = ['storage', '=', $storage];

        $data = FileService::list($where, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 文件信息
     */
    public function read()
    {
        $param['file_id'] = input('get.file_id/d', 0);

        validate(FileValidate::class)->scene('info')->check($param);

        $data = FileService::info($param['file_id']);
        if (!$data) {
            throw new MissException('文件已被删除：' . $param['file_id']);
        }

        return success($data);
    }

    /**
     * 文件添加
     */
    public function save()
    {
        $param['file'] = request()->file('file');
        $param['group_id'] = input('group_id/d', 0);
        $param['file_type'] = input('file_type/s', 'image');
        $param['file_name'] = input('file_name/s', '');
        $param['is_front'] = input('is_front/d', false);
        $param['sort'] = input('sort/d', 250);

        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);

        return success($data, '上传成功');
    }

    /**
     * 文件修改
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
     * 文件删除
     */
    public function delete()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('del')->check($param);

        $data = FileService::del($param['ids']);

        return success($data);
    }

    /**
     * 文件修改分组
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
     * 文件修改类型
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
     * 文件是否禁用
     */
    public function disable()
    {
        $param['ids'] = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', false);

        validate(FileValidate::class)->scene('disable')->check($param);

        $data = FileService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * 文件回收站
     */
    public function recover()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        // 检索字段
        $file_id = input('file_id/s', '');
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        $group_id = input('group_id/d', 0);
        $file_type = input('file_type/s', 0);
        $is_disable = input('is_disable/b');
        $is_front = input('is_front/b');
        $storage = input('storage/s', '');

        // 构建查询条件
        $where = [];
        if (!empty($file_id)) $where[] = ['', 'exp', Db::raw("FIND_IN_SET(file_id,'" . $file_id . "')")];
        if (!empty($search_words)) $where[] = ['file_name|file_md5|file_hash', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if (!empty($group_id)) $where[] = ['group_id', '=', $group_id];
        if (!empty($file_type)) $where[] = ['file_type', '=', $file_type];
        if (is_bool($is_disable)) $where[] = ['is_disable', '=', $is_disable];
        if (is_bool($is_front)) $where[] = ['is_front', '=', $is_front];
        if (!empty($storage)) $where[] = ['storage', '=', $storage];


        $data = FileService::list($where, $current, $pageSize, $order, '', true);

        return success($data);
    }

    /**
     * 文件回收站恢复
     */
    public function recoverReco()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('reco')->check($param);

        $data = FileService::recoverReco($param['ids']);

        return success($data);
    }

    /**
     * 文件回收站删除
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('del')->check($param);

        $data = FileService::recoverDele($param['ids']);

        return success($data);
    }
}
