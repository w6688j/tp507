<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/30
 * Time: 22:49
 */

namespace app\api\validate;

use think\Validate;

class TestValidate extends Validate
{
    protected $rule = [
        'name'  => 'require|max:10',
        'email' => 'email',
    ];
}