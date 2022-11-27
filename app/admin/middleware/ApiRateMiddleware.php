<?php
/**
 * Description: 接口速率中间件
 * File: ApiRateMiddleware.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\admin\middleware;

use app\common\exception\FrequentException;
use Closure;
use think\Request;
use think\Response;
use app\common\cache\admin\ApiRateCache;
use app\common\service\admin\SettingService;

class ApiRateMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws FrequentException
     */
    public function handle($request, Closure $next)
    {
        $setting = SettingService::info();
        $api_rate_num = $setting['api_rate_num'];
        $api_rate_time = $setting['api_rate_time'];

        if ($api_rate_num > 0 && $api_rate_time > 0) {
            $admin_user_id = user_id();
            $menu_url = menu_url();

            if ($admin_user_id && $menu_url) {
                if (!menu_is_unrate($menu_url)) {
                    $count = ApiRateCache::get($admin_user_id, $menu_url);
                    if ($count) {
                        if ($count >= $api_rate_num) {
                            ApiRateCache::del($admin_user_id, $menu_url);
                            throw new FrequentException();
                        }
                        else {
                            ApiRateCache::inc($admin_user_id, $menu_url);
                        }
                    }
                    else {
                        ApiRateCache::set($admin_user_id, $menu_url, $api_rate_time);
                    }
                }
            }
        }

        return $next($request);
    }
}
