<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/11
 * Time: 11:13
 */

namespace app\lib\exception;

class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;
}