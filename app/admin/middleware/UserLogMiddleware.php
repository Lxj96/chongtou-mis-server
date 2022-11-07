<?php
/**
 * Description: 日志记录中间件
 * File: UserLogMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\admin\UserLogService;

class UserLogMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $admin_user_id = admin_user_id();

        if ($admin_user_id) {

            $response_data = $response->getData();

            if (isset($response_data['code'])) {
                $admin_user_log['response_code'] = $response_data['code'];
            }
            else {
                $admin_user_log['response_code'] = $response->getCode();
            }
            if (isset($response_data['msg'])) {
                $admin_user_log['response_msg'] = $response_data['msg'];
            }
            else if (isset($response_data['message'])) {
                $admin_user_log['response_msg'] = $response_data['message'];
            }

            $admin_user_log['admin_user_id'] = $admin_user_id;
            UserLogService::add($admin_user_log);
        }

        return $response;
    }
}
