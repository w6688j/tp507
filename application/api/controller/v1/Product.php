<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/3
 * Time: 18:25
 */

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;

class Product
{
    /**
     * getRecent 最新商品列表
     *
     * @url    product/recent?count=16
     * @http   GET
     *
     * @param int $count 新品数量
     *
     * @author wangjian
     * @time   2018/6/3 18:28
     * @return mixed
     * @throws \think\Exception
     */
    public function getRecent($count = 16)
    {
        (new Count())->goCheck();

        $products = ProductModel::getMostRecent($count);
        if ($products->isEmpty()) {
            throw new ProductException();
        }

        //方法一：临时隐藏，特定业务需求整体隐藏列表中每条记录的特定字段,避免影响整体，$collection是数据集对象
        /*$collection = collection($products);
        $products   = $collection->hidden(['summary']);*/

        //方法二：修改database配置文件中的resultset_type为collection
        $products->hidden(['summary']);

        return $products;
    }

    /**
     * getAllInCategory 获取所属分类的产品
     *
     * @param int $id 分类id
     *
     * @author wangjian
     * @time   2018/6/4 9:16
     *
     * @return mixed
     * @throws \think\Exception
     */
    public function getAllInCategory($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if ($products->isEmpty()) {
            throw new ProductException();
        }
        $products->hidden(['summary']);

        return $products;
    }

    /**
     * getOne 获取商品详情
     *
     * @param int $id 商品id
     *
     * @author wangjian
     * @time   2018/6/5 12:02
     *
     * @return array|false|mixed|\PDOStatement|string|\think\Model
     * @throws ProductException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOne($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if (!$product) {
            throw new ProductException();
        }

        return $product;
    }
}