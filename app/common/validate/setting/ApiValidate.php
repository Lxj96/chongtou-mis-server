<?php
/**
 * Description: 接口管理验证器
 * File: ApiValidate.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\validate\setting;

use think\Validate;
use app\common\model\setting\ApiModel;

class ApiValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        'api_id' => ['require'],
        'api_name' => ['require', 'checkApiExist'],
    ];

    // 错误信息
    protected $message = [
        'api_name.require' => '请输入接口名称',
    ];

    // 验证场景
    protected $scene = [
        'id' => ['api_id'],
        'info' => ['api_id'],
        'add' => ['api_name'],
        'edit' => ['api_id', 'api_name'],
        'dele' => ['ids'],
        'pid' => ['ids'],
        'unlogin' => ['ids'],
        'disable' => ['ids'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkApiPid');
    }

    // 验证场景定义：修改上级
    protected function scenepid()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkApiPidNeq');
    }

    // 自定义验证规则：接口上级不能等于接口本身
    protected function checkApiPidNeq($value, $rule, $data = [])
    {
        foreach ($data['ids'] as $v) {
            if ($data['api_pid'] == $v) {
                return '接口上级不能等于接口本身';
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否已存在
    protected function checkApiExist($value, $rule, $data = [])
    {
        $ApiModel = new ApiModel();
        $ApiPk = $ApiModel->getPk();

        $api_id = isset($data[$ApiPk]) ? $data[$ApiPk] : '';
        if ($api_id) {
            if ($data['api_pid'] == $data[$ApiPk]) {
                return '接口上级不能等于接口本身';
            }
        }

        $name_where[] = [$ApiPk, '<>', $api_id];
        $name_where[] = ['api_pid', '=', $data['api_pid']];
        $name_where[] = ['api_name', '=', $data['api_name']];
        $name_where[] = ['is_delete', '=', 0];
        $api_name = $ApiModel->field($ApiPk)->where($name_where)->find();
        if ($api_name) {
            return '接口名称已存在：' . $data['api_name'];
        }

        if ($data['api_url']) {
            $url_where[] = [$ApiPk, '<>', $api_id];
            $url_where[] = ['api_url', '=', $data['api_url']];
            $url_where[] = ['is_delete', '=', 0];
            $api_url = $ApiModel->field($ApiPk)->where($url_where)->find();
            if ($api_url) {
                return '接口链接已存在：' . $data['api_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否存在下级接口
    protected function checkApiPid($value, $rule, $data = [])
    {
        $ApiModel = new ApiModel();
        $ApiPk = $ApiModel->getPk();

        $where[] = ['api_pid', 'in', $data['ids']];
        $where[] = ['is_delete', '=', 0];
        $api = $ApiModel->field($ApiPk)->where($where)->find();
        if ($api) {
            return '接口存在下级接口，无法删除';
        }

        return true;
    }
}
