<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/21
 * Time: 11:19
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Pay as PayService;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\WxNotify;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder'],
    ];

    /**
     * getPreOrder 预支付订单
     *
     * @param int $id 订单id
     *
     * @author wangjian
     * @time   2018/6/23 15:27
     *
     * @return array
     * @throws \WxPayException
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPreOrder($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        return (new PayService($id))->pay();
    }

    /**
     * receiveNotify 接收回调
     *
     * @author wangjian
     * @time   2018/6/23 19:13
     */
    public function receiveNotify()
    {
        $xmlData = file_get_contents('php://input');

        return curl_post_raw('https://mp.w6688j.com/api/v1/pay/re_notify?XDEBUG_SESSION_START=15876', $xmlData);
    }

    /**
     * redirectNotify 接收转发回调
     *
     * @author wangjian
     * @time   2018/6/23 18:53
     */
    public function redirectNotify()
    {
        // 通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒

        // 检测库存量，超卖
        // 更新订单status状态
        // 减库存
        // 如果成功处理，返回微信成功处理的信息；否则，返回未成功的处理

        // 特点：post xml格式 不会携带参数
        return (new WxNotify())->Handle();
    }
}