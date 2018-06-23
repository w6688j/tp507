<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/21
 * Time: 11:27
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use think\Loader;
use think\Log;

// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    /**
     * Pay constructor.
     *
     * @param int $orderID 订单id
     *
     * @throws Exception
     */
    public function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为NULL');
        }

        $this->orderID = $orderID;
    }

    /**
     * pay 支付
     *
     * @author wangjian
     * @time   2018/6/23 14:26
     * @return array
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \WxPayException
     */
    public function pay()
    {
        // 订单号不存在
        // 订单号存在，但是订单号与当前用户不匹配
        // 订单已支付
        $this->checkOrderValid();
        // 库存量检测
        $orderService = new OrderService();
        $status       = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }

        return $this->makeWxPreOrder($status['orderPrice']);
    }

    /**
     * checkOrderValid 验证订单
     *
     * @author wangjian
     * @time   2018/6/23 14:14
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @return bool
     */
    private function checkOrderValid()
    {
        $order = (new OrderModel())->where('id', '=', $this->orderID)->find();
        if (!$order) {
            throw new OrderException();
        }

        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException([
                'msg'       => '订单与当前用户不匹配',
                'errorCode' => 10003,
            ]);
        }

        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg'       => '订单已支付',
                'errorCode' => 80003,
                'code'      => 400,
            ]);
        }

        $this->orderNO = $order->order_no;

        return true;
    }

    /**
     * makeWxPreOrder 组合微信预支付订单数据
     *
     * @param $totalPrice
     *
     * @author wangjian
     * @time   2018/6/23 15:19
     *
     * @return mixed
     * @throws Exception
     * @throws TokenException
     * @throws \WxPayException
     */
    private function makeWxPreOrder($totalPrice)
    {
        // openid
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }

        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url('https://mp.w6688j.com');

        return $this->getPaySignature($wxOrderData);
    }

    /**
     * getPaySignature 调用微信支付接口生成微信预支付订单
     *
     * @param \WxPayUnifiedOrder $wxOrderData 微信订单对象
     *
     * @return mixed
     * @throws Exception
     * @throws WeChatException
     * @throws \WxPayException
     * @author wangjian
     * @time   2018/6/23 15:10
     *
     */
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
            throw new WeChatException([
                'msg' => '获取预支付订单失败',
            ]);
        }

        // prepay_id
        $this->recordPreOrder($wxOrder);

        return $this->sign($wxOrder);
    }

    /**
     * recordPreOrder 记录prepay_id
     *
     * @param array $wxOrder 微信订单返回数组
     *
     * @author wangjian
     * @time   2018/6/23 16:09
     */
    private function recordPreOrder($wxOrder)
    {
        (new OrderModel())
            ->where('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    /**
     * sign 生成小程序端调用微信支付的参数
     *
     * @param array $wxOrder 微信订单返回数组
     *
     * @author wangjian
     * @time   2018/6/23 16:46
     * @return array
     */
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign                 = $jsApiPayData->MakeSign();
        $rawValues            = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['appId']);

        return $rawValues;
    }
}