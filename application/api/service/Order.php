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
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
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
        $orderSnap     = $this->snapOrder($status);
        $order         = $this->createOrder($orderSnap);
        $order['pass'] = true;

        return $order;
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

        return Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
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
            $status['totalCount'] += $pStatus['counts'];
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
            'id'           => null,
            'haveStock'    => false,
            'counts'       => 0,
            'price'        => 0,
            'name'         => '',
            'totalPrice'   => 0,
            'main_img_url' => null,
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

        $pStatus['id']           = $product['id'];
        $pStatus['name']         = $product['name'];
        $pStatus['counts']       = $oCount;
        $pStatus['price']        = $product['price'];
        $pStatus['main_img_url'] = $product['main_img_url'];
        $pStatus['totalPrice']   = $product['price'] * $oCount;
        $pStatus['haveStock']    = ($product['stock'] >= $oCount);

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
        $snap['pStatus']     = $status['pStatusArray'];
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
        Db::startTrans();
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
            Db::commit();

            return [
                'order_no'    => $orderNo,
                'order_id'    => $order->id,
                'create_time' => $order->create_time,
            ];
        } catch (Exception $e) {
            Db::rollback();
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

    /**
     * checkOrderStock 检查库存
     *
     * @param int $orderID 订单ID
     *
     * @return array
     * @throws Exception
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/21 11:41
     *
     */
    public function checkOrderStock($orderID)
    {
        $oProducts       = (new OrderProduct())->where('order_id', '=', $orderID)->select()->toArray();
        $this->oProducts = $oProducts;
        $this->products  = $this->getProductsByOrder($oProducts);

        return $this->getOrderStatus();
    }

    /**
     * delivery
     *
     * @param        $orderID
     * @param string $jumpPage
     *
     * @author wangjian
     * @time   2018/9/18 20:48
     *
     * @throws Exception
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @return bool
     */
    public function delivery($orderID, $jumpPage = '')
    {
        $order = (new OrderModel)->where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg'       => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code'      => 403,
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
        $message = new DeliveryMessage();

        return $message->sendDeliveryMessage($order, $jumpPage);
    }

    // 创建订单时没有预扣除库存量，简化处理
    // 如果预扣除了库存量需要队列支持，且需要使用锁机制
    private function createOrderByTrans($snap)
    {
        try {
            $orderNo             = $this->makeOrderNo();
            $order               = new OrderModel();
            $order->user_id      = $this->uid;
            $order->order_no     = $orderNo;
            $order->total_price  = $snap['orderPrice'];
            $order->total_count  = $snap['totalCount'];
            $order->snap_img     = $snap['snapImg'];
            $order->snap_name    = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items   = json_encode($snap['pStatus']);
            $order->save();

            $orderID     = $order->id;
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            return [
                'order_no'    => $orderNo,
                'order_id'    => $orderID,
                'create_time' => $create_time,
            ];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // 单个商品库存检测
    private function snapProduct($product, $oCount)
    {
        $pStatus = [
            'id'           => null,
            'name'         => null,
            'main_img_url' => null,
            'count'        => $oCount,
            'totalPrice'   => 0,
            'price'        => 0,
        ];

        $pStatus['counts'] = $oCount;
        // 以服务器价格为准，生成订单
        $pStatus['totalPrice']   = $oCount * $product['price'];
        $pStatus['name']         = $product['name'];
        $pStatus['id']           = $product['id'];
        $pStatus['main_img_url'] = $product['main_img_url'];
        $pStatus['price']        = $product['price'];

        return $pStatus;
    }
}