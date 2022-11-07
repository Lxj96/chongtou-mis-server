<?php
/**
 * Description: 权限验证中间件
 * File: AuthVerifyMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\middleware;

use app\common\exception\AuthException;
use app\common\exception\ForbiddenException;
use app\common\exception\MissException;
use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\common\cache\admin\UserCache;
use app\common\service\admin\MenuService;

class AuthVerifyMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws MissException
     * @throws AuthException
     * @throws ForbiddenException
     */
    public function handle($request, Closure $next)
    {
        $menu_url = menu_url();
        
        // 菜单是否存在
        if (!menu_is_exist($menu_url)) {
            $msg = '接口地址错误';
            $debug = Config::get('app.app_debug');
            if ($debug) {
                $msg .= '：' . $menu_url;
            }

            throw new MissException($msg);
        }


        // 菜单是否无需权限
        if (!menu_is_unauth($menu_url)) {

            $admin_user_id = admin_user_id();

            // 用户是否超管
            if (!admin_is_super($admin_user_id)) {
                $user = UserCache::get($admin_user_id);
                /*if (empty($user)) {
                    throw new AuthException('登录已失效，请重新登录');
                }

                if ($user['is_disable'] == 1) {
                    throw new AuthException('账号已禁用，请联系管理员');
                }*/

                if (!in_array($menu_url, $user['roles'])) {
                    $menu = MenuService::info($menu_url);
                    throw new ForbiddenException('你没有权限操作：' . $menu['menu_name']);
                }
            }
        }

        return $next($request);
    }
}
