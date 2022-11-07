<?php
/**
 * Description: 留言管理模型
 * File: CommentModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class CommentModel extends Model
{
    // 表名
    protected $name = 'cms_comment';
    // 表主键
    protected $pk = 'comment_id';

    /**
     * @Apidoc\Field("comment_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("comment_id,call,mobile,tel,title,remark,is_read,create_time,update_time,delete_time")
     */
    public function listReturn()
    {
    }

    /**
     *
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("call,mobile,tel,email,qq,wechat,title,content")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("comment_id,remark")
     */
    public function editParam()
    {
    }
}
