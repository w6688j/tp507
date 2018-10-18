<?php
/**
 * Created by PhpStorm.
 * User: WJ
 * Date: 2018/10/18
 * Time: 18:49
 */

namespace app\api\validate;

class FrontRegister extends BaseValidate
{
    protected $rule = [
        'username' => 'require|isNotEmpty',
        'password' => 'require|isNotEmpty',
    ];
}