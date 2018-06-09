<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/5
 * Time: 11:54
 */

namespace app\api\model;

class ProductProperty extends BaseModel
{
    protected $hidden = [
        'id',
        'product_id',
        'delete_time',
        'update_time',
    ];
}