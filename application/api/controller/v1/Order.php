<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/11
 * Time: 12:14
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope'   => ['only' => 'getSummaryByUser,getDetail'],
    ];

    // 用户在选择商品后，向API提交包含所选择商品的信息
    // API在接收到商品信息后，需要检查订单相关商品的库存量
    // 有库存，把订单数据存入数据库 = 下单成功，返回客户端可以支付
    // 调用支付接口
    // 再次进行库存量检测
    // 服务器调用微信支付接口进行支付
    // 小程序根据服务器返回的结果拉起微信支付
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

    /**
     * getSummaryByUser 分页获取订单列表
     *
     * @param int $page 当前页
     * @param int $size 分页大小
     *
     * @author wangjian
     * @time   2018/6/23 19:58
     *
     * @return mixed
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid          = TokenService::getCurrentUID();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty()) {
            return [
                'data'         => [],
                'current_page' => $pagingOrders->getCurrentPage(),
            ];
        }

        return [
            'data'         => $pagingOrders->hidden(['snap_items', 'snap_address', 'prepay_id'])->toArray(),
            'current_page' => $pagingOrders->getCurrentPage(),
        ];
    }

    /**
     * getDetail 获取订单详情
     *
     * @param int $id 订单id
     *
     * @author wangjian
     * @time   2018/6/23 20:15
     *
     * @return mixed
     * @throws OrderException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }

        return $orderDetail->hidden(['prepay_id']);
    }

    /**
     * 获取全部订单简要信息（分页）
     *
     * @param int $page
     * @param int $size
     *
     * @throws \think\Exception
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page = 1, $size = 20)
    {
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty()) {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data'         => [],
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();

        return [
            'current_page' => $pagingOrders->currentPage(),
            'data'         => $data,
        ];
    }

    /**
     * delivery
     *
     * @param $id
     *
     * @author wangjian
     * @time   2018/9/18 20:47
     *
     * @throws \think\Exception
     * @return SuccessMessage
     */
    public function delivery($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $order   = new OrderService();
        $success = $order->delivery($id);
        if ($success) {
            return new SuccessMessage();
        }
    }
}