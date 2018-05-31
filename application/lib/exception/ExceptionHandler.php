<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 12:28
 */

namespace app\lib\exception;

use Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    // 需要返回客户端当前请求的URL路径

    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            // 如果是自定义异常
            $this->code      = $e->code;
            $this->msg       = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code      = 500;
                $this->msg       = 'Service error~';
                $this->errorCode = 999;
                $this->recordError($e);
            }
        }

        return json([
            'msg'         => $this->msg,
            'error_code'  => $this->errorCode,
            'request_url' => Request::instance()->url(),
        ], $this->code);
    }

    /**
     * recordError 记录日志
     *
     * @param Exception $e 错误
     *
     * @author wangjian
     * @time   2018/5/31 14:02
     */
    private function recordError(Exception $e)
    {
        Log::init([
            'type'  => 'File',
            'path'  => LOG_PATH,
            'level' => ['error'],
        ]);
        Log::record($e->getMessage(), 'error');
    }
}