<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/23
 * Time: 19:46
 */

namespace app\api\validate;

class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'size' => 'isPositiveInteger',
    ];

    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数',
    ];
}