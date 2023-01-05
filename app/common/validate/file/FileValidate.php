<?php
/**
 * Description: 文件管理验证器
 * File: FileValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\file;

use think\Validate;
use app\common\service\file\SettingService;

class FileValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'file' => ['require', 'file', 'checkLimit'],
        'file_id' => ['require'],
        'file_type' => ['require'],
        'file_name' => ['require'],
        'file_path' => ['require'],
        'flag' => ['require'],
        'group_id' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'file.require' => '请选择上传文件',
        'file_id.require' => '缺少参数：file_id',
        'file_type.require' => '请选择文件类型',
        'group_id.require' => '缺少参数：group_id',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['file_id'],
        'info' => ['file_id'],
        'add' => ['file'],
        'edit' => ['file_id'],
        'del' => ['ids'],
        'disable' => ['ids'],
        'editgroup' => ['ids'],
        'edittype' => ['ids', 'file_type'],
        'editdomain' => ['ids'],
        'reco' => ['ids'],
        'log' => ['flag', 'file_id', 'file_name', 'file_path'],
    ];

    // 自定义验证规则：上传限制
    protected function checkLimit($value, $rule, $data = [])
    {
        $file = $data['file'];
        $setting = SettingService::info();

        $file_ext = $file->getOriginalExtension();
        if (empty($file_ext)) {
            return '上传的文件格式不允许';
        }

        $file_type = SettingService::getFileType($file_ext);
        $set_ext_str = $setting[$file_type . '_ext'];
        $set_ext_arr = explode(',', $set_ext_str);
        if (!in_array($file_ext, $set_ext_arr)) {
            return '上传的文件格式不允许，允许格式：' . $set_ext_str;
        }

        $file_size = $file->getSize();
        $set_size_m = $setting[$file_type . '_size'];
        $set_size_b = $set_size_m * 1048576;
        if ($file_size > $set_size_b) {
            return '上传的文件大小不允许，允许大小：' . $set_size_m . ' MB';
        }

        return true;
    }
}
