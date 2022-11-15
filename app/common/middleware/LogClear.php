<?php
/**
 * Description: 日志清除中间件
 * File: LogClear.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\middleware;

use app\common\cache\admin\UserLogCache;
use app\common\service\admin\SettingService as AdminSettingService;
use app\common\service\admin\UserLogService;
use Closure;
use think\Request;
use think\Response;

class LogClear
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
        // 用户日志清除
        $admin_setting = AdminSettingService::info();
        if ($admin_setting['log_save_time']) {
            $user_clear_key = 'clear';
            $user_clear_val = UserLogCache::get($user_clear_key);
            if (empty($user_clear_val)) {
                $user_days = $admin_setting['log_save_time'];
                $user_date = date('Y-m-d H:i:s', strtotime("-{$user_days} day"));
                $user_where[] = ['create_time', '<=', $user_date];
                UserLogService::clear($user_where);
                UserLogCache::set($user_clear_key, $user_days, 86400);
            }
        }

        return $next($request);
    }
}
