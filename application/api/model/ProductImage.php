<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/5
 * Time: 11:51
 */

namespace app\api\model;

class ProductImage extends BaseModel
{
    protected $hidden = [
        'img_id',
        'product_id',
        'delete_time',
    ];

    /**
     * imgUrl 关联模型 一对一 图片URL
     *
     * @author wangjian
     * @time   2018/6/5 11:56
     * @return \think\model\relation\BelongsTo
     */
    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}