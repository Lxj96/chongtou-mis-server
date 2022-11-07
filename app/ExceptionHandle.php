<?php
/**
 * Description: 应用异常处理类
 * File: ExceptionHandle.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app;

use app\common\exception\BaseException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Config;
use think\Response;
use Throwable;

class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
        BaseException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制

        // 手动异常
        if ($e instanceof BaseException) {
            //如果是自定义异常，则控制http状态码，不需要记录日志
            //因为这些通常是因为客户端传递参数错误或者是用户请求造成的异常
            //不应当记录日志
            $result = [
                'success' => false,
                'code' => $e->code,
                'data' => [],
                'message' => $e->message,
                'showType' => $e->showType,
                //'traceId' => '',// 方便后端故障排除：唯一的请求ID
                'host' => $request->host(),// 后端故障排除的便利条件：当前访问服务器的主机
                'request_url' => $request->url()
            ];
            return json($result, $e->code);
        }

        // 参数验证错误
        if ($e instanceof ValidateException) {
            $result = [
                'success' => false,
                'code' => 422,
                'data' => [],
                'message' => $e->getError(),
                'showType' => 2,
                //'traceId' => '',// 方便后端故障排除：唯一的请求ID
                'host' => $request->host(),// 后端故障排除的便利条件：当前访问服务器的主机
                'request_url' => $request->url()
            ];

            return json($result, 422);
        }

        // 请求异常
        if ($e instanceof HttpException && $request->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode(), [], 'json');
        }

        $debug = Config::get('app.app_debug');
        if ($debug) {
            // 其他错误交给系统处理
            return parent::render($request, $e);
        }
        else {
            // 其他异常

            $result = [
                'success' => false,
                'code' => $e->getCode(),
                'data' => [],
                'message' => $e->getMessage(),
                'showType' => 2,
                //'traceId' => '',// 方便后端故障排除：唯一的请求ID
                'host' => $request->host(),// 后端故障排除的便利条件：当前访问服务器的主机
                'request_url' => $request->url()
            ];
            return json($result, $e->getCode());
        }
    }
}
