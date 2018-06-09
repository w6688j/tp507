<?php

namespace app\api\model;

use think\Collection;

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
     * imgs 关联模型 一对多 商品图片
     *
     * @author wangjian
     * @time   2018/6/5 11:48
     * @return \think\model\relation\HasMany
     */
    public function imgs()
    {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    /**
     * properties 关联模型 一对多 商品属性
     *
     * @author wangjian
     * @time   2018/6/5 11:50
     * @return \think\model\relation\HasMany
     */
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }

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

    /**
     * getMostRecent 获取最新产品列表
     *
     * @param int $count 列表产品数
     *
     * @author wangjian
     * @time   2018/6/3 18:42
     *
     * @return Collection|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMostRecent($count)
    {
        return (new self())
            ->limit($count)
            ->order('create_time,id desc')
            ->select();
    }

    /**
     * getProductsByCategoryID 根据分类id获取产品
     *
     * @param int $categoryID 分类id
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/4 9:20
     *
     */
    public static function getProductsByCategoryID($categoryID)
    {
        return (new self())->where('category_id', '=', $categoryID)->select();
    }

    /**
     * getProductDetail 根据商品ID获取商品详情
     *
     * @param int $id 商品id
     *
     * @author wangjian
     * @time   2018/6/5 12:00
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductDetail($id)
    {
        return (new self())->with(['imgs', 'imgs.imgUrl', 'properties'])->find($id);
    }
}