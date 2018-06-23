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
}