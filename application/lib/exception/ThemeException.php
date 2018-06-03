<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/3
 * Time: 16:41
 */

namespace app\lib\exception;

class ThemeException extends BaseException
{
    public $code = 404;
    public $msg = '指定的主题不存在，请检查主题ID';
    public $errorCode = 30000;
}