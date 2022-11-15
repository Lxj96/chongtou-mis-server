<?php
/**
 * Description: 科室文档
 * File: FileService.php
 * User: Lxj
 * DateTime: 2022-11-15 13:08
 */

namespace app\common\service\department;


use app\common\cache\department\FileCache;
use app\common\cache\department\FileGroupCache;
use app\common\exception\SaveErrorMessage;
use app\common\exception\UploadErrorException;
use app\common\model\department\FileGroupModel;
use app\common\model\department\FileModel;
use app\common\service\file\SettingService;
use app\common\service\file\StorageService;
use think\facade\Filesystem;

class FileService
{
    /**
     * 文件列表
     *
     * @param int $flag 分类
     * @param int $current 当前页
     * @param int $pageSize 每页记录数
     * @param array $order 排序
     *
     * @return array
     */
    public static function list($flag = 1, $current = 1, $pageSize = 10, $order = [])
    {
        // 检索字段
        $search_words = input('search_words/s', '');
        $date_field = input('date_field/s', '');
        $date_value = input('date_value/a', []);
        $group_id = input('group_id/d', 0);
        $file_type = input('file_type/s', 0);
        $is_disable = input('is_disable/b');

        // 构建查询条件
        $where = [
            ['flag', '=', $flag]
        ];
        if (!empty($search_words)) $where[] = ['file_name', 'like', '%' . $search_words . '%'];
        if ($date_field && !empty($date_value)) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if (!empty($group_id)) {
            $group_ids = self::getSubordinate([$group_id], $group_id);
            $where[] = ['group_id', 'in', $group_ids];
        }
        if (!empty($file_type)) $where[] = ['file_type', '=', $file_type];
        if (is_bool($is_disable)) $where[] = ['is_disable', '=', $is_disable];

        $model = new FileModel();
        $pk = $model->getPk();

        $GroupModel = new FileGroupModel();
        $GroupPk = $GroupModel->getPk();

        $field = $pk . ',' . $GroupPk . ',storage,domain,file_md5,file_hash,file_type,file_name,file_path,file_size,file_ext,sort,is_disable';

        if (empty($order)) {
            $order = ['update_time' => 'desc', 'sort' => 'desc', $pk => 'desc'];
        }

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
     * @return array
     * @throws UploadErrorException
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
            ->putFile('department_file', $file, function () use ($file_hash) {
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

        $file_exist = $model->field($pk)->where('flag', $param['flag'])->where('file_hash', $file_hash)->find();
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
     * @return array
     * @throws UploadErrorException
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
     * 文件分组
     * @param integer $flag 文件类型
     * @return array
     */
    public static function group($flag)
    {
        $data = FileGroupCache::get('flag' . $flag);
        if (empty($data)) {
            $model = new FileGroupModel();
            $where = [
                ['flag', '=', $flag],
                ['is_disable', '=', 0]
            ];
            $field = 'group_id,group_name,group_pid,group_sort,is_disable';

            $order = ['group_sort' => 'desc', 'group_id' => 'asc'];

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();

            $data = self::toTree($data, 0);

            FileGroupCache::set('flag' . $flag, $data);
        }

        return ['list' => $data];
    }

    /**
     * 菜单信息
     *
     * @param integer|string $id 分组id
     * @param integer $flag 类型
     * @param bool $exce 不存在是否抛出异常
     *
     * @return array
     * @throws MissException
     */
    public static function groupInfo($id = 0, $flag = 1, $exce = true)
    {
        $info = FileGroupCache::get($id);
        if (empty($info)) {
            $model = new FileGroupModel();

            $where[] = ['group_id', '=', $id];
            $where[] = ['flag', '=', $flag];

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    throw new MissException('分组不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            FileGroupCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 文件分组添加
     *
     * @param array $param 文件分组信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function groupAdd($param)
    {
        $model = new FileGroupModel();

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            throw new SaveErrorMessage();
        }

        $param['id'] = $id;

        FileGroupCache::del('flag' . $param['flag']);
        return $param;
    }

    /**
     * 文件分组修改
     *
     * @param array $param 文件分组信息
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function groupEdit($param)
    {
        $model = new FileGroupModel();

        $id = $param['group_id'];
        unset($param['group_id']);

        $param['update_time'] = datetime();

        $res = $model->where('group_id', $id)->update($param);
        if (empty($res)) {
            throw new SaveErrorMessage();
        }

        FileGroupCache::del('flag' . $param['flag']);
        FileGroupCache::del($id);

        $param['group_id'] = $id;

        return $param;
    }

    /**
     * 文件分组是否禁用
     *
     * @param array $group_id id
     * @param int $flag 类型
     *
     * @return array
     * @throws SaveErrorMessage
     */
    public static function groupDisable($group_id, $flag)
    {
        $model = new FileGroupModel();

        $update['is_disable'] = 1;
        $update['update_time'] = datetime();

        $model->startTrans();
        try {
            $ids = self::getSubordinate([$group_id], $group_id);
            $res = $model->where('group_id', 'in', $ids)->where('flag', '=', $flag)->update($update);
            if (empty($res)) {
                throw new SaveErrorMessage();
            }
            $model->commit();
        } catch (\Exception $e) {
            $model->rollback();
            throw new SaveErrorMessage();
        }

        FileGroupCache::del('flag' . $flag);
        foreach ($ids as $val) {
            FileGroupCache::del($val);
        }

        return $ids;
    }


    /**
     * 迭代获取所有下级
     */
    public static function getSubordinate($ids = [], $data)
    {
        if (!is_array($data)) {
            $data = FileGroupModel::field('group_id')
                ->where(array('group_pid' => $data))
                ->where('is_disable', 0)
                ->select();
        }

        if (!empty($data)) {
            foreach ($data as $v) {
                $ids[] = $v['group_id'];
                $xj = FileGroupModel::field('group_id')
                    ->where(array('group_pid' => $v['group_id']))
                    ->where('is_disable', 0)
                    ->select()
                    ->toArray();
                if (!empty($xj)) {
                    $ids = self::getSubordinate($ids, $xj);
                }
            }
            return $ids;
        }
        else {
            return $ids;
        }
    }

    /**
     * 菜单列表转树形
     *
     * @param array $group 分组列表
     * @param int $group_pid 分组pid
     *
     * @return array
     */
    public static function toTree($group, $group_pid)
    {
        $tree = [];
        foreach ($group as $v) {
            if ($v['group_pid'] == $group_pid) {
                $v['children'] = self::toTree($group, $v['group_id']);
                if (empty($v['children'])) unset($v['children']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}