<?php
/**
 * Description: 行政村文档
 * File: File.php
 * User: Lxj
 * DateTime: 2022-11-13 20:05
 */

namespace app\admin\controller\village;


use app\common\exception\MissException;
use app\common\service\file\SettingService;
use app\common\service\village\FileService;
use app\common\validate\village\FileValidate;

class File
{
    /**
     * 文档目录
     */
    public function group()
    {
        $param['flag'] = input('get.flag/d', 0);

        validate(FileValidate::class)->scene('group')->check($param);

        $data = FileService::group($param['flag']);
        $data['filetype'] = SettingService::fileType();
        $data['storage'] = SettingService::storage();
        return success($data);
    }

    /**
     * 文档目录信息
     */
    public function groupRead()
    {
        $param['flag'] = input('get.flag/d', 0);
        $param['group_id'] = input('get.group_id/d', 0);
        validate(FileValidate::class)->scene('groupinfo')->check($param);

        $data = FileService::groupInfo($param['group_id'], $param['flag']);
        if (empty($data)) {
            throw new MissException('分组已被删除：' . $param['group_id']);
        }

        return success($data);
    }

    /**
     * 文档目录新增
     */
    public function groupSave()
    {
        $param['flag'] = input('flag/d', 0);
        $param['group_pid'] = input('group_pid/d', 0);
        $param['group_name'] = input('group_name/s', '');
        $param['power_grade'] = input('power_grade/d', 1);
        $param['group_sort'] = input('group_sort/d', 250);

        validate(FileValidate::class)->scene('groupadd')->check($param);

        $data = FileService::groupAdd($param);

        return success($data);
    }

    /**
     * 文档目录修改
     */
    public function groupUpdate()
    {
        $param['group_id'] = input('group_id/d', 0);
        $param['flag'] = input('flag/d', 0);
        $param['group_pid'] = input('group_pid/d', 0);
        $param['group_name'] = input('group_name/s', '');
        $param['power_grade'] = input('power_grade/d', 1);
        $param['group_sort'] = input('group_sort/d', 250);

        validate(FileValidate::class)->scene('groupedit')->check($param);

        $data = FileService::groupEdit($param);

        return success($data);
    }

    /**
     * 文档目录禁用
     */
    public function groupDisable()
    {
        $param['group_id'] = input('group_id/d', 0);
        $param['flag'] = input('flag/d', 0);

        validate(FileValidate::class)->scene('groupdisable')->check($param);

        $data = FileService::groupDisable($param['group_id'], $param['flag']);

        return success($data);
    }

    /**
     * 文档目录删除
     */
    public function groupDelete()
    {

    }

    /**
     * 应急管理
     */
    public function contingency()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(1, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 地质灾害
     */
    public function geology()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(2, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 卫健
     */
    public function health()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(3, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 环境卫生整治
     */
    public function environment()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(4, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 民政
     */
    public function politics()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(5, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 三农工作
     */
    public function peasantry()
    {
        // 列表通用字段
        $current = input('current/d', 1);
        $pageSize = input('pageSize/d', 10);
        $order = input('sort/a', [], 'format_sort');
        $group_id = input('group_id/d', 0);

        validate(FileValidate::class)->scene('list')->check(['group_id' => $group_id]);

        $data = FileService::list(6, $current, $pageSize, $order);

        return success($data);
    }

    /**
     * 文件信息
     */
    public function read()
    {
        $param['flag'] = input('flag/d', 0);
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
        $param['flag'] = input('flag/d', 0);
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
        $param['flag'] = input('flag/d', 0);
        $param['file_id'] = input('file_id/d', '');
        $param['group_id'] = input('group_id/d', 0);
        $param['file_name'] = input('file_name/s', '');
        $param['sort'] = input('sort/d', 250);

        validate(FileValidate::class)->scene('edit')->check($param);

        $data = FileService::edit($param);

        return success($data);
    }

    /**
     * 文件删除
     */
    public function delete()
    {
        $param['flag'] = input('flag/d', 0);
        $param['ids'] = input('ids/a', '');

        validate(FileValidate::class)->scene('del')->check($param);

        $data = FileService::del($param['ids']);

        return success($data);
    }

    /**
     * 文件修改分组
     */
    public function editgroup()
    {
        $param['flag'] = input('flag/d', 0);
        $param['ids'] = input('ids/a', '');
        $param['group_id'] = input('group_id/d', 0);

        validate(FileValidate::class)->scene('editgroup')->check($param);

        $data = FileService::editgroup($param['ids'], $param['group_id']);

        return success($data);
    }

    /**
     * 文件修改排序
     */
    public function updateSort()
    {
        $param['data'] = input('data/a', []);

        $data = FileService::updateSort($param['data']);

        return success();

    }
}