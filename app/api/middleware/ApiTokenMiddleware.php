<?php
/**
 * Description: 接口Token中间件
 * File: ApiTokenMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;

class ApiTokenMiddleware
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
        // 接口是否无需登录
        if (!api_is_unlogin()) {

            // 接口token是否已设置
            if (!api_token_has()) {
                exception('Requests Headers：Token must');
            }

            // 接口token是否为空
            if (empty(api_token())) {
                exception('请登录', 401);
            }

            // 接口token验证
            api_token_verify();
        }

        return $next($request);
    }
}
