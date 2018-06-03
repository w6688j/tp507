<?php

namespace app\api\model;

class Product extends BaseModel
{
    protected $hidden = [
        'category_id',
        'from',
        'img_id',
        'pivot',
        'create_time',
        'delete_time',
        'update_time',
    ];

    /**
     * getMainImgUrlAttr 读取器
     *
     * @param string $value 值
     * @param array  $data  记录其它信息
     *
     * @author wangjian
     * @time   2018/6/3 18:10
     *
     * @return string
     */
    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}