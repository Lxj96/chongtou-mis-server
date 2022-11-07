<?php
/**
 * Description: 文件设置控制器
 * File: Setting.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\controller\file;

use app\common\service\file\SettingService;
use app\common\validate\file\SettingValidate;
use think\response\Json;

class Setting
{
    /**
     * 文件设置信息
     *
     * @return Json
     */
    public function read()
    {
        $data['setting'] = SettingService::info();
        $data['storage'] = SettingService::storage();

        return success($data);
    }

    /**
     * 文件设置修改
     *
     * @return Json
     * @throws \app\common\exception\MissException
     */
    public function update()
    {
        $param['storage'] = input('storage/s', 'local');
        $param['qiniu_access_key'] = input('qiniu_access_key/s', '');
        $param['qiniu_secret_key'] = input('qiniu_secret_key/s', '');
        $param['qiniu_bucket'] = input('qiniu_bucket/s', '');
        $param['qiniu_domain'] = input('qiniu_domain/s', '');
        $param['aliyun_access_key_id'] = input('aliyun_access_key_id/s', '');
        $param['aliyun_access_key_secret'] = input('aliyun_access_key_secret/s', '');
        $param['aliyun_bucket'] = input('aliyun_bucket/s', '');
        $param['aliyun_bucket_domain'] = input('aliyun_bucket_domain/s', '');
        $param['aliyun_endpoint'] = input('aliyun_endpoint/s', '');
        $param['tencent_secret_id'] = input('tencent_secret_id/s', '');
        $param['tencent_secret_key'] = input('tencent_secret_key/s', '');
        $param['tencent_bucket'] = input('tencent_bucket/s', '');
        $param['tencent_region'] = input('tencent_region/s', '');
        $param['tencent_domain'] = input('tencent_domain/s', '');
        $param['baidu_access_key'] = input('baidu_access_key/s', '');
        $param['baidu_secret_key'] = input('baidu_secret_key/s', '');
        $param['baidu_bucket'] = input('baidu_bucket/s', '');
        $param['baidu_endpoint'] = input('baidu_endpoint/s', '');
        $param['baidu_domain'] = input('baidu_domain/s', '');
        $param['image_ext'] = input('image_ext/s', '');
        $param['image_size'] = input('image_size/s', 0);
        $param['video_ext'] = input('video_ext/s', '');
        $param['video_size'] = input('video_size/s', 0);
        $param['audio_ext'] = input('audio_ext/s', '');
        $param['audio_size'] = input('audio_size/s', 0);
        $param['word_ext'] = input('word_ext/s', '');
        $param['word_size'] = input('word_size/s', 0);
        $param['other_ext'] = input('other_ext/s', '');
        $param['other_size'] = input('other_size/s', 0);

        validate(SettingValidate::class)->scene($param['storage'])->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
