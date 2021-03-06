<?php
/**
 * Created by 七月
 * Author: 七月
 * 微信公号: 小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/3/7
 * Time: 13:27
 */

namespace app\api\service;

use app\api\model\User;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID = 'your wx template ID';// 小程序模板消息ID号

    /**
     * sendDeliveryMessage
     *
     * @param        $order
     * @param string $tplJumpPage
     *
     * @author wangjian
     * @time   2018/9/18 20:38
     *
     * @throws OrderException
     * @throws \think\Exception
     * @return bool
     */
    public function sendDeliveryMessage($order, $tplJumpPage = '')
    {
        if (!$order) {
            throw new OrderException();
        }
        $this->tplID  = self::DELIVERY_MSG_ID;
        $this->formID = $order->prepay_id;
        $this->page   = $tplJumpPage;
        $this->prepareMessageData($order);
        $this->emphasisKeyWord = 'keyword2.DATA';

        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }

    /**
     * prepareMessageData
     *
     * @param $order
     *
     * @author wangjian
     * @time   2018/9/18 20:38
     * @return void
     */
    private function prepareMessageData($order)
    {
        $dt         = new \DateTime();
        $data       = [
            'keyword1' => [
                'value' => '顺风速运',
            ],
            'keyword2' => [
                'value' => $order->snap_name,
                'color' => '#27408B',
            ],
            'keyword3' => [
                'value' => $order->order_no,
            ],
            'keyword4' => [
                'value' => $dt->format("Y-m-d H:i"),
            ],
        ];
        $this->data = $data;
    }

    /**
     * getUserOpenID
     *
     * @param $uid
     *
     * @throws UserException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @return mixed
     * @author wangjian
     * @time   2018/9/18 20:38
     *
     */
    private function getUserOpenID($uid)
    {
        $user = User::get($uid);
        if (!$user) {
            throw new UserException();
        }

        return $user->openid;
    }
}