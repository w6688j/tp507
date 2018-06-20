<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/20
 * Time: 10:43
 */

namespace app\api\service;

use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Exception;

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
     * @return array
     * @throws OrderException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/20 11:00
     *
     * @throws \Exception
     */
    public function place($uid, $oProducts)
    {
        // oProducts与products做对比
        // 从数据库中查询出products
        $this->oProducts = $oProducts;
        $this->products  = $this->getProductsByOrder($oProducts);
        $this->uid       = $uid;

        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;

            return $status;
        }

        // 开始创建订单
        $orderSnap = $this->snapOrder($status);

        return $this->createOrder($orderSnap);
    }

    /**
     * getProductsByOrder
     *
     * @param array $oProducts 订单的商品列表
     *
     * @author wangjian
     * @time   2018/6/20 10:58
     *
     * @return mixed
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

    /**
     * getOrderStatus 获取订单状态
     *
     * @author wangjian
     * @time   2018/6/20 11:58
     * @return array
     * @throws OrderException
     * @throws \think\Exception
     */
    private function getOrderStatus()
    {
        $status = [
            'pass'         => true,
            'orderPrice'   => 0,
            'totalCount'   => 0,
            'pStatusArray' => [],
        ];

        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],
                $oProduct['count'],
                $this->products);

            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }

            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }

        return $status;
    }

    /**
     * getProductStatus 获取产品状态
     *
     * @param int   $oPID     订单中的产品id
     * @param int   $oCount   订单中的产品数量
     * @param array $products 订单中的产品数组
     *
     * @author wangjian
     * @time   2018/6/20 11:53
     *
     * @return array
     * @throws OrderException
     * @throws \think\Exception
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex  = -1;
        $pStatus = [
            'id'         => null,
            'haveStock'  => false,
            'count'      => 0,
            'name'       => '',
            'totalPrice' => 0,
        ];

        for ($i = 0; $i < count($products); $i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            // 客户端传递的productid有可能不存在
            throw new OrderException([
                'msg' => 'id为' . $oPID . '的商品不存在，创建订单失败',
            ]);
        }

        $product = $products[$pIndex];

        $pStatus['id']         = $product['id'];
        $pStatus['name']       = $product['name'];
        $pStatus['count']      = $oCount;
        $pStatus['totalPrice'] = $product['price'] * $oCount;
        $pStatus['haveStock']  = ($product['stock'] >= $oCount);

        return $pStatus;
    }

    /**
     * snapOrder 生成订单快照
     *
     * @param array $status 订单状态
     *
     * @return array
     * @throws UserException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/20 12:29
     */
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice'  => 0,
            'totalCount'  => 0,
            'pStatus'     => [],
            'snapAddress' => null,
            'snapName'    => '',
            'snapImg'     => '',
        ];

        $snap['orderPrice']  = $status['orderPrice'];
        $snap['totalCount']  = $status['totalCount'];
        $snap['pStatus']     = $status['pStatus'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName']    = $this->products[0]['name'];
        $snap['snapImg']     = $this->products[0]['main_img_url'];

        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }

        return $snap;
    }

    /**
     * getUserAddress 获取用户地址
     *
     * @author wangjian
     * @time   2018/6/20 12:36
     * @return array
     * @throws UserException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getUserAddress()
    {
        $userAddress = (new UserAddress())->where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg'       => '用户收货地址不存在，下单失败',
                'errorCode' => 60001,
            ]);
        }

        return $userAddress->toArray();
    }

    /**
     * createOrder 订单写入数据库
     *
     * @param array $snap 订单快照数组
     *
     * @author wangjian
     * @time   2018/6/20 13:03
     * @return array
     * @throws \Exception
     */
    private function createOrder($snap)
    {
        try {
            $orderNo             = self::makeOrderNo();
            $order               = new OrderModel();
            $order->user_id      = $this->uid;
            $order->order_no     = $orderNo;
            $order->total_price  = $snap['orderPrice'];
            $order->total_count  = $snap['totalCount'];
            $order->snap_name    = $snap['snapName'];
            $order->snap_img     = $snap['snapImg'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items   = json_encode($snap['pStatus']);

            $order->save();

            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $order->id;
            }

            (new OrderProduct())->saveAll($this->oProducts);

            return [
                'order_no'    => $orderNo,
                'order_id'    => $order->id,
                'create_time' => $order->create_time,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * makeOrderNo 生成订单号
     *
     * @author wangjian
     * @time   2018/6/20 12:50
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y') - 2017)] . strtoupper(dechex(date('m'))) .
            date('d') . substr(time(), -5) . substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));

        return $orderSn;
    }
}