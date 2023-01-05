<?php
/**
 * Description: 通用文稿
 * File: FileModel.php
 * User: Lxj
 * DateTime: 2022-11-15 13:07
 */

namespace app\common\model\currency;


use app\common\model\BaseModel;
use app\common\service\file\SettingService;

class FileModel extends BaseModel
{
    // 表名
    protected $name = 'currency_file';
    // 表主键
    protected $pk = 'file_id';

    protected $type = [
        'is_front' => 'boolean',
        'is_disable' => 'boolean',
    ];

    public function getFileUrlAttr($value, $data)
    {
        $file_url = $data['file_path'];
        if (!$data['is_disable']) {
            if ($data['storage'] == 'local') {
                $file_url = file_url($data['file_path']);
            }
            else {
                $file_url = $data['domain'] . '/' . $data['file_hash'] . '.' . $data['file_ext'];
            }
        }
        return $file_url;
    }

    public function getFileSizeAttr($value, $data)
    {
        return SettingService::fileSize($value);
    }
}