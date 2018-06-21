<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/20
 * Time: 12:55
 */

namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
}