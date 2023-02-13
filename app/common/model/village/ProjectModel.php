<?php
/**
 * Description: 村域项目
 * File: ProjectModel.php
 * User: Lxj
 * DateTime: 2022-11-14 19:06
 */

namespace app\common\model\village;


use app\common\model\BaseModel;

class ProjectModel extends BaseModel
{
    // 表名
    protected $name = 'village_project';
    protected $type = [
        'start_time' => 'timestamp:Y-m-d',
        'end_time' => 'timestamp:Y-m-d',
    ];

    /**
     * 关联File模型
     */
    public function file()
    {
        return $this->hasOne('app\common\model\file\FileModel', 'file_id', 'file_id');
    }
}