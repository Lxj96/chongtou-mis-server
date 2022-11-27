<?php
/**
 * Description: 个人中心验证器
 * File: UserCenterValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\admin;

use think\Validate;
use app\common\model\admin\UserModel;

class UserCenterValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'user_id' => ['require'],
        'username' => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname' => ['require', 'checkNickname', 'length' => '1,32'],
        'password_old' => ['require'],
        'password_new' => ['require', 'length' => '4,18'],
        'phone' => ['mobile', 'checkPhone'],
        'email' => ['email', 'checkEmail'],
    ];

    // 错误信息
    protected $message = [
        'username.require' => '请输入账号',
        'username.length' => '账号长度为2至32个字符',
        'nickname.require' => '请输入昵称',
        'nickname.length' => '昵称长度为1至32个字符',
        'password_old.require' => '请输入旧密码',
        'password_new.require' => '请输入新密码',
        'password_new.length' => '新密码长度为6至18个字符',
        'phone.mobile' => '请输入正确的手机号码',
        'email.email' => '请输入正确的邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['user_id'],
        'info' => ['user_id'],
        'edit' => ['user_id', 'username', 'nickname', 'phone', 'email'],
        'pwd' => ['user_id', 'password_old', 'password_new'],
        'log' => ['user_id'],
    ];

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $user_where[] = [$UserPk, '<>', $data[$UserPk]];
        $user_where[] = ['username', '=', $data['username']];
        $user = $UserModel->field($UserPk)->where($user_where)->find();
        if ($user) {
            return '账号已存在：' . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $user_where[] = [$UserPk, '<>', $data[$UserPk]];
        $user_where[] = ['nickname', '=', $data['nickname']];
        $user = $UserModel->field($UserPk)->where($user_where)->find();
        if ($user) {
            return '昵称已存在：' . $data['nickname'];
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $user_where[] = [$UserPk, '<>', $data[$UserPk]];
        $user_where[] = ['phone', '=', $data['phone']];
        $user = $UserModel->field($UserPk)->where($user_where)->find();
        if ($user) {
            return '手机已存在：' . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $user_where[] = [$UserPk, '<>', $data[$UserPk]];
        $user_where[] = ['email', '=', $data['email']];
        $user = $UserModel->field($UserPk)->where($user_where)->find();
        if ($user) {
            return '邮箱已存在：' . $data['email'];
        }

        return true;
    }
}
