<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/20
 * Time: 11:40
 */

namespace app\lib\exception;

class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 80000;
}