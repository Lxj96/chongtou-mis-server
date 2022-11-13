<?php
/**
 * Description: 文件管理
 * File: FileService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\file;

use app\common\cache\file\FileCache;
use app\common\exception\SaveErrorMessage;
use app\common\exception\UploadErrorException;
use app\common\model\file\FileModel;
use app\common\model\file\GroupModel;
use think\facade\Db;
use think\facade\Filesystem;

class FileService
{
    /**
     * 文件列表
     *
     * @param array $where 条件
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     * @param string $field 字段
     * @param boolean $onlyTrashed 是否只查询软删除的数据
     *
     * @return array
     */
    public static function list($where = [], $current = 1, $pageSize = 10, $order = [], $field = '', $onlyTrashed = false)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $GroupPk . ',storage,domain,file_md5,file_hash,file_type,file_name,file_path,file_size,file_ext,sort,is_disable';
        }
        else {
            $field = str_merge($field, 'file_id,storage,domain,file_md5,file_hash,file_path,file_ext,is_disable');
        }

        if (empty($order)) {
            $order = ['update_time' => 'desc', 'sort' => 'desc', $pk => 'desc'];
        }

        if ($onlyTrashed) {
            $total = $model->onlyTrashed()->where($where)->count($pk);

            $pages = ceil($total / $pageSize);

            $list = $model->field($field)
                ->append(['file_url'])
                ->onlyTrashed()
                ->where($where)
                ->page($current)
                ->limit($pageSize)
                ->order($order)
                ->select()
                ->toArray();
        }
        else {

            $total = $model->where($where)->count($pk);

            $pages = ceil($total / $pageSize);

            $list = $model->field($field)
                ->append(['file_url'])
                ->where($where)
                ->page($current)
                ->limit($pageSize)
                ->order($order)
                ->select()
                ->toArray();
        }

        $ids = array_column($list, $pk);

        return compact('total', 'pages', 'current', 'pageSize', 'list', 'ids');
    }

    /**
     * 文件信息
     *
     * @param int $id 文件id
     *
     * @return array
     */
    public static function info($id)
    {
        if (empty($id)) {
            return [];
        }

        $info = FileCache::get($id);
        if (empty($info)) {
            $model = new FileModel();
            $info = $model->field('*')->append(['file_url'])->find($id);
            if (empty($info)) {
                return [];
            }
            else {
                $info = $info->toArray();
                FileCache::set($id, $info);
            }
        }

        return $info;
    }

    /**
     * 文件添加
     *
     * @param array $param 文件信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $file = $param['file'];
        unset($param['file']);
        $datetime = datetime();

        $file_ext = $file->getOriginalExtension();
        $file_type = SettingService::getFileType($file_ext);
        $file_size = $file->getSize();
        $file_md5 = $file->hash('md5');
        $file_hash = $file->hash('sha1');
        $file_name = Filesystem::disk('public')
            ->putFile('file', $file, function () use ($file_hash) {
                return $file_hash;
            });

        $param['file_md5'] = $file_md5;
        $param['file_hash'] = $file_hash;
        $param['file_path'] = 'storage/' . $file_name;
        $param['file_ext'] = $file_ext;
        $param['file_size'] = $file_size;
        $param['file_type'] = $file_type;
        if (empty($param['file_name'])) {
            $param['file_name'] = mb_substr($file->getOriginalName(), 0, -(mb_strlen($param['file_ext']) + 1));
        }

        // 对象存储
        $param = StorageService::upload($param);

        $model = new FileModel();
        $pk = $model->getPk();

        $file_exist = $model->field($pk)->where('file_hash', $file_hash)->find();
        if ($file_exist) {
            $file_exist = $file_exist->toArray();
            $file_update[$pk] = $file_exist[$pk];
            $file_update['storage'] = $param['storage'];
            $file_update['domain'] = $param['domain'];
            $file_update['is_disable'] = 0;
            self::edit($file_update);
            $id = $file_exist[$pk];
        }
        else {
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $id = $model->strict(false)->insertGetId($param);
            if (empty($id)) {
                throw new UploadErrorException();
            }
        }

        $info = self::info($id);

        return $info;
    }

    /**
     * 文件修改
     *
     * @param array $param 文件信息
     *
     * @return array|Exception
     */
    public static function edit($param)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            throw new UploadErrorException();
        }

        FileCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 文件删除
     *
     * @param array $ids 文件id
     * @param int $is_delete 是否删除
     *
     * @throws SaveErrorMessage
     */
    public static function del($ids, $is_delete = 1)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改分组
     *
     * @param array $ids 文件id
     * @param int $group_id 分组id
     *
     * @throws SaveErrorMessage
     */
    public static function editgroup($ids, $group_id = 0)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['group_id'] = $group_id;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改类型
     *
     * @param array $ids 文件id
     * @param string $file_type 文件类型
     *
     * @throws SaveErrorMessage
     */
    public static function edittype($ids, $file_type = 'image')
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['file_type'] = $file_type;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件修改域名
     *
     * @param array $ids 文件id
     * @param string $domain 文件域名
     *
     */
    public static function editdomain($ids, $domain = '')
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['domain'] = $domain;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件是否禁用
     *
     * @param array $ids 文件id
     * @param int $is_disable 是否禁用
     *
     */
    public static function disable($ids, $is_disable = 0)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['is_disable'] = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }


    /**
     * 文件回收站恢复
     *
     * @param array $ids 文件id
     * @throws SaveErrorMessage
     */
    public static function recoverReco($ids)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $update['delete_time'] = null;
        $update['update_time'] = datetime();

        $res = $model->onlyTrashed()->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        foreach ($ids as $v) {
            FileCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 文件回收站删除
     *
     * @param array $ids 文件id
     * @throws SaveErrorMessage
     */
    public static function recoverDele($ids)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $file = $model->field($pk . ',file_path')->onlyTrashed()->where($pk, 'in', $ids)->select();

        $res = Db::name('file')->where($pk, 'in', $ids)->delete();

        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        $del = [];
        foreach ($file as $v) {
            FileCache::del($v[$pk]);
            $del[] = @unlink($v['file_path']);
        }

        $update['ids'] = $ids;
        $update['del'] = $del;

        return $update;
    }

    /**
     * 文件链接
     *
     * @param mixed $file 文件id、信息
     *
     * @return string
     */
    public static function fileUrl($file)
    {
        if (is_numeric($file)) {
            $file = self::info($file);
        }

        $file_url = '';
        if ($file) {
            if (!$file['is_disable']) {
                if ($file['storage'] == 'local') {
                    $file_url = file_url($file['file_path']);
                }
                else {
                    $file_url = $file['domain'] . '/' . $file['file_hash'] . '.' . $file['file_ext'];
                }
            }
        }

        return $file_url;
    }

    /**
     * 文件数组
     *
     * @param string $ids 文件id，逗号,隔开
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function fileArray($ids)
    {
        $model = new FileModel();
        $pk = $model->getPk();

        $field = $pk . ',storage,domain,file_type,file_name,file_hash,file_ext,file_path,file_size,is_disable';
        $where[] = [$pk, 'in', $ids];
        $where[] = ['is_disable', '=', 0];
        $file = $model->field($field)->append(['file_url'])->where($where)->select()->toArray();

        return $file;
    }

    /**
     * 文件统计
     *
     * @return array
     */
    public static function statistics()
    {
        $key = 'count';
        $data = FileCache::get($key);
        if (empty($data)) {
            $model = new FileModel();
            $pk = $model->getPk();

            $file_types = SettingService::fileType();
            $file_field = 'file_type,count(file_type) as count';
            $file_count = $model->field($file_field)->group('file_type')->select()->toArray();
            foreach ($file_types as $k => $v) {
                $temp = [];
                $temp['name'] = $v;
                $temp['value'] = 0;
                foreach ($file_count as $vfc) {
                    if ($k == $vfc['file_type']) {
                        $temp['value'] = $vfc['count'];
                    }
                }
                $data['data'][] = $temp;
            }
            $data['count'] = $model->count($pk);

            FileCache::set($key, $data);
        }

        return $data;
    }
}
