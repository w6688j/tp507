<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 12:29
 */

namespace app\lib\exception;

use think\Exception;

class BaseException extends Exception
{
    //HTTP 状态码 404,200
    public $code = 400;

    // 错误具体信息
    public $msg = 'param is error';

    // 自定义错误码
    public $errorCode = 10000;

    /**
     * BaseException constructor.
     *
     * @param array $params 参数
     *
     * @throws Exception
     */
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            throw new Exception('参数必须是数组');
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}