<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/11
 * Time: 12:14
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\OrderPlace;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'createOrUpdateAddress'],
    ];

    // 用户在选择商品后，向API提交包含所选择商品的信息
    // API在接收到商品信息后，需要检查订单相关商品的库存量
    // 有库存，把订单数据存入数据库 = 下单成功，返回客户端可以支付
    // 调用支付接口
    // 再次进行库存量检测
    // 服务器调用微信支付接口进行支付
    // 微信返回支付结果（异步）
    // 成功：也需要再次进行库存量检测
    // 成功：进行库存量的扣除

    /**
     * placeOrder 下单
     *
     * @author wangjian
     * @time   2018/6/15 8:17
     * @throws \think\Exception
     */
    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid      = TokenService::getCurrentUID();

        return ((new OrderService())->place($uid, $products));
    }
}