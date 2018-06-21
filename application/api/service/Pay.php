<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/21
 * Time: 11:27
 */

namespace app\api\service;

use think\Exception;

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

    public function pay()
    {

    }
}