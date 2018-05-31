<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 14:38
 */

namespace app\lib\exception;

use Throwable;

class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = 'param is error';
    public $errorCode = 10000;
}