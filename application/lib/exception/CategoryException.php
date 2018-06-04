<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 9:00
 */

namespace app\lib\exception;

class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定的分类不存在，请检查参数';
    public $errorCode = 50000;
}