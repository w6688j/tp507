<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/9
 * Time: 18:29
 */

namespace app\lib\exception;

class SuccessMessage extends BaseException
{
    public $code = 201;
    public $msg = 'ok';
    public $errorCode = 0;
}