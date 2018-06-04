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
}