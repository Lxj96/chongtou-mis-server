<?php
/**
 * Description: 文件设置
 * File: SettingService.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\service\file;

use app\common\cache\file\SettingCache;
use app\common\exception\MissException;
use app\common\model\file\SettingModel;

class SettingService
{
    // 文件设置id
    private static $id = 1;

    /**
     * 文件设置信息
     *
     * @return array
     */
    public static function info()
    {
        $id = self::$id;
        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk] = $id;
                $info['create_time'] = datetime();
                $model->insert($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 文件设置修改
     *
     * @param array $param 设置信息
     *
     * @return array
     * @throws MissException
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $pk = $model->getPk();

        $id = self::$id;

        $param['update_time'] = datetime();

        $info = $model->where($pk, $id)->update($param);
        if (empty($info)) {
            throw new MissException();
        }

        SettingCache::del($id);

        return $param;
    }

    /**
     * 文件类型数组
     *
     * @return array
     */
    public static function fileType()
    {
        $filetype = [
            [
                'label' => '图片',
                'value' => 'image'
            ],
            [
                'label' => '视频',
                'value' => 'video'
            ],
            [
                'label' => '音频',
                'value' => 'audio'
            ],
            [
                'label' => '文档',
                'value' => 'word'
            ],
            [
                'label' => '其它',
                'value' => 'other'
            ],
        ];

        return $filetype;
    }

    /**
     * 文件储存方式
     *
     * @return array
     */
    public static function storage()
    {
        $storage = [
            [
                'label' => '本地(服务器)',
                'value' => 'local'
            ],
            [
                'label' => '七牛云Kodo',
                'value' => 'qiniu'
            ],
            [
                'label' => '阿里云OSS',
                'value' => 'aliyun'
            ],
            [
                'label' => '腾讯云COS',
                'value' => 'tencent'
            ],
            [
                'label' => '百度云BOS',
                'value' => 'baidu'
            ],
        ];

        return $storage;
    }

    /**
     * 文件大小格式化
     *
     * @param int $file_size 文件大小（byte(B)字节）
     *
     * @return string
     */
    public static function fileSize($file_size = 0)
    {
        $p = 0;
        $format = 'B';
        if ($file_size > 0 && $file_size < 1024) {
            $p = 0;
            return number_format($file_size) . ' ' . $format;
        }
        elseif ($file_size >= 1024 && $file_size < pow(1024, 2)) {
            $p = 1;
            $format = 'KB';
        }
        elseif ($file_size >= pow(1024, 2) && $file_size < pow(1024, 3)) {
            $p = 2;
            $format = 'MB';
        }
        elseif ($file_size >= pow(1024, 3) && $file_size < pow(1024, 4)) {
            $p = 3;
            $format = 'GB';
        }
        elseif ($file_size >= pow(1024, 4) && $file_size < pow(1024, 5)) {
            $p = 3;
            $format = 'TB';
        }

        $file_size /= pow(1024, $p);

        return number_format($file_size, 2) . ' ' . $format;
    }

    /**
     * 文件类型获取
     *
     * @param string $file_ext 文件后缀
     *
     * @return string image图片，video视频，audio音频，word文档，other其它
     */
    public static function getFileType($file_ext = '')
    {
        if ($file_ext) {
            $file_ext = strtolower($file_ext);
        }

        $image_ext = [
            'jpg', 'png', 'jpeg', 'gif', 'bmp', 'webp', 'ico', 'svg', 'tif', 'pcx', 'tga', 'exif',
            'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'raw', 'wmf', 'avif', 'apng', 'xbm', 'fpx'
        ];
        $video_ext = [
            'mp4', 'avi', 'mkv', 'flv', 'rm', 'rmvb', 'webm', '3gp', 'mpeg', 'mpg', 'dat', 'asx', 'wmv',
            'mov', 'm4a', 'ogm', 'vob'
        ];
        $audio_ext = [
            'mp3', 'aac', 'wma', 'wav', 'ape', 'flac', 'ogg', 'adt', 'adts', 'cda', 'cd', 'wave',
            'aiff', 'midi', 'ra', 'rmx', 'vqf', 'amr'
        ];
        $word_ext = [
            'doc', 'docx', 'docm', 'dotx', 'dotm', 'txt',
            'xls', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam', 'csv',
            'ppt', 'pptx', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm', 'thmx'
        ];

        if (in_array($file_ext, $image_ext)) {
            return 'image';
        }
        elseif (in_array($file_ext, $video_ext)) {
            return 'video';
        }
        elseif (in_array($file_ext, $audio_ext)) {
            return 'audio';
        }
        elseif (in_array($file_ext, $word_ext)) {
            return 'word';
        }
        else {
            return 'other';
        }
    }
}
