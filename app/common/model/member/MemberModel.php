<?php
/**
 * Description: 会员管理模型
 * File: MemberModel.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class MemberModel extends Model
{
    // 表名
    protected $name = 'member';
    // 表主键
    protected $pk = 'member_id';

    /**
     * @Apidoc\Field("member_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("member_id,username,nickname,phone,email,remark,sort,create_time,login_time,is_disable")
     * @Apidoc\AddField("avatar_url", type="string", require=false, desc="头像链接")
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
     * @Apidoc\Field("avatar_id,username,nickname,password,phone,email,region_id,remark,sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("member_id,avatar_id,username,nickname,phone,email,region_id,remark,sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("username")
     */
    public function username()
    {
    }

    /**
     * @Apidoc\Field("nickname")
     */
    public function nickname()
    {
    }

    /**
     * @Apidoc\Field("phone")
     */
    public function phone()
    {
    }

    /**
     * @Apidoc\Field("email")
     */
    public function email()
    {
    }

    /**
     * @Apidoc\Field("password")
     */
    public function password()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }

    /**
     * @Apidoc\Field("avatar_url")
     * @Apidoc\AddField("avatar_url", type="string", require=false, desc="头像链接")
     */
    public function avatar_url()
    {
    }

    /**
     * 账号注册
     * @Apidoc\Field("username,nickname,password")
     */
    public function registerReturn()
    {
    }

    /**
     * @Apidoc\Field("member_id,username,nickname,phone,email,avatar_id,login_ip,login_time")
     * @Apidoc\AddField("menber_token", type="string", require=true, desc="MemberToken")
     */
    public function loginReturn()
    {
    }

    /**
     * @Apidoc\WithoutField("password,remark,sort,is_disable,is_delete,logout_time,delete_time")
     * @Apidoc\AddField("wechat", type="object", default="", desc="微信信息")
     * @Apidoc\AddField("pwd_edit_type", type="int", default="0", desc="密码修改方式：0原密码设置新密码，1直接设置新密码")
     */
    public function indexInfoReturn()
    {
    }

    /**
     * 会员修改信息
     * @Apidoc\Field("avatar_id,username,nickname,region_id")
     */
    public function indexEditParam()
    {
    }
}
