<?php
/**
 * Description: Token验证中间件
 * File: TokenVerifyMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\middleware;

use app\common\exception\AuthException;
use app\common\service\admin\TokenService;
use Closure;
use think\Request;
use think\Response;

class TokenVerifyMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws AuthException
     */
    public function handle($request, Closure $next)
    {
        // 菜单无需登录
        if (!menu_is_unlogin()) {
            $admin_token = admin_token();

            if (empty($admin_token)) {
                throw new AuthException('Requests Headers：AdminToken must');
            }

            // 用户Token验证
            TokenService::verify($admin_token);

        }

        return $next($request);
    }
}
