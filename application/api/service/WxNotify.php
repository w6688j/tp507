<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/23
 * Time: 18:06
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    /**
     * NotifyProcess 回调方法
     *
     * @param array  $data
     * @param string $msg
     *
     * @return bool \true回调出来完成不需要继续回调，false回调处理未完成需要继续回调|void
     * @throws \app\lib\exception\OrderException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/23 18:14
     *
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try {
                $order = (new OrderModel())
                    ->where('order_no', '=', $orderNo)
                    ->lock(true)
                    ->find();

                if ($order->status == OrderStatusEnum::UNPAID) {
                    $stockStatus = (new OrderService())->checkOrderStock($order->id);
                    if ($stockStatus['pass']) {
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($stockStatus);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }

                Db::commit();

                return true;
            } catch (Exception $e) {
                Db::rollback();
                Log::error($e);

                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * updateOrderStatus 更新订单状态
     *
     * @param int  $orderID 订单id
     * @param bool $success true：库存检测通过；false：库存检测失败
     *
     * @author wangjian
     * @time   2018/6/23 18:24
     */
    public function updateOrderStatus($orderID, $success)
    {
        $status = $success ?
            OrderStatusEnum::PAID :
            OrderStatusEnum::PAID_BUT_OUT_OF;

        (new OrderModel())
            ->where('id', '=', $orderID)
            ->update(['status' => $status]);
    }

    /**
     * reduceStock 减库存
     *
     * @param array $stockStatus 订单各产品库存数组
     *
     * @author wangjian
     * @time   2018/6/23 18:37
     *
     * @throws \think\Exception
     */
    public function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {
            (new Product())
                ->where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }
}