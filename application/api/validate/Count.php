<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/3
 * Time: 18:31
 */

namespace app\api\validate;

class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPostiveInteger|between:1,20',
    ];

    protected $message = [
        'count' => 'count必须为正整数且count只能在1~20之间',
    ];
}