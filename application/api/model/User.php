<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 10:08
 */

namespace app\api\model;

class User extends BaseModel
{
    /**
     * address 关联模型
     *
     * @author wangjian
     * @time   2018/6/9 18:21
     * @return \think\model\relation\HasOne
     */
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    /**
     * getByOpenId 根据openId查询用户数据
     *
     * @param string $openid 微信用户openId
     *
     * @author wangjian
     * @time   2018/6/4 19:39
     *
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getByOpenId($openid)
    {
        $user = (new self())->where('openid', '=', $openid)->find();

        return $user;
    }
}