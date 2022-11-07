<?php
/**
 * Description: 跨域请求中间件
 * File: AllowCrossDomain.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\middleware;

use Closure;
use think\Request;
use think\Response;

class AllowCrossDomain
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
        header('Access-Control-Allow-Origin: *');// 允许所有访问地址
        header('Access-Control-Allow-Headers: *');// 设置你要请求的头部信息
//        header('Content-type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, HEAD');// 设置你请求的方式
        header('Access-Control-Allow-Credentials:false');// 设置允许cookie跨域，这里如果设置为true上面就不能允许所有的地址了，要改为指定地址

        if ($request->isOptions()) {
            return Response::create();
        }

        return $next($request);
    }
}
