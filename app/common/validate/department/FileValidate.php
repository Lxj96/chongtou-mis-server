<?php
/**
 * Description: 科室文档验证器
 * File: FileValidate.php
 * User: Lxj
 * DateTime: 2022-11-15 13:05
 */

namespace app\common\validate\department;


use app\common\service\file\SettingService;
use think\Validate;

class FileValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'flag' => ['require', 'eq:1'],
        'group_pid' => ['require', 'integer'],
        'group_id' => ['require', 'integer', 'gt:0'],
        'group_name' => ['require'],
        'file' => ['require', 'file', 'checkLimit'],
        'file_id' => ['require'],
        'file_type' => ['require'],
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
        'group' => ['flag'],
        'groupadd' => ['flag', 'group_pid', 'group_name'],
        'groupedit' => ['group_id', 'flag', 'group_pid', 'group_name'],
        'groupinfo' => ['flag', 'group_id'],
        'groupdisable' => ['flag', 'group_id'],
        'list' => ['group_id'],
        'info' => ['flag', 'file_id'],
        'add' => ['flag', 'group_id', 'file'],
        'edit' => ['file_id', 'flag', 'group_id'],
        'del' => ['flag', 'ids'],
        'disable' => ['ids'],
        'editgroup' => ['flag', 'ids', 'group_id'],
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