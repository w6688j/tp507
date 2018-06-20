<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/20
 * Time: 10:43
 */

namespace app\api\service;

use app\api\model\Product;

class Order
{
    // 订单的商品列表，也就是客户端传递过来的products参数
    protected $oProducts;
    // 数据库查询出来的商品信息（包括库存量）
    protected $products;

    protected $uid;

    /**
     * place 下单服务
     *
     * @param int   $uid       用户id
     * @param array $oProducts 订单的商品列表
     *
     * @author wangjian
     * @time   2018/6/20 11:00
     *
     * @throws \think\exception\DbException
     */
    public function place($uid, $oProducts)
    {
        // oProducts与products做对比
        // 从数据库中查询出products
        $this->oProducts = $oProducts;
        $this->products  = $this->getProductsByOrder($oProducts);
        $this->uid       = $uid;
    }

    /**
     * getProductsByOrder
     *
     * @param array $oProducts 订单的商品列表
     *
     * @author wangjian
     * @time   2018/6/20 10:58
     *
     * @throws \think\exception\DbException
     */
    private function getProductsByOrder($oProducts)
    {
        $oPIDs = [];
        foreach ($oProducts as $item) {
            array_push($oPIDs, $item['product_id']);
        }
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
    }
}