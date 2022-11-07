<?php
/**
 * Description: 会员日志中间件
 * File: MemberLogMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\member\LogService;

class MemberLogMiddleware
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

        $member_id = member_id();
        if ($member_id) {
            $response_data = $response->getData();
            if (isset($response_data['code'])) {
                $member_log['response_code'] = $response_data['code'];
            }
            if (isset($response_data['msg'])) {
                $member_log['response_msg'] = $response_data['msg'];
            }
            else {
                if (isset($response_data['message'])) {
                    $member_log['response_msg'] = $response_data['message'];
                }
            }
            $member_log['member_id'] = $member_id;
            LogService::add($member_log);
        }

        return $response;
    }
}
