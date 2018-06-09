<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/9
 * Time: 19:04
 */

namespace app\api\model;

class UserAddress extends BaseModel
{
    protected $hidden = [
        'id',
        'delete_time',
        'user_id',
    ];
}