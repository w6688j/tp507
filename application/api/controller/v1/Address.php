<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/9
 * Time: 17:09
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    /**
     * @var array  前置方法
     */
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress'],
    ];

    /**
     * createOrUpdateAddress 创建或更新用户地址
     *
     * @author wangjian
     * @time   2018/6/9 18:31
     * @return SuccessMessage
     * @throws UserException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        // 根据Token获取用户UID
        $uid = TokenService::getCurrentUID();
        // 根据UID查找用户数据，判断用户是否存在，如果不存在抛出异常
        $user = UserModel::get($uid);
        if (!$user)
            throw new UserException();
        // 获取用户从客户端提交的地址信息
        $dataArray = $validate->getDataByRule(input('post.'));
        // 根据用户地址信息是否存在判断是添加地址还是更新地址
        $userAddress = $user->address;
        if (!$userAddress)
            $user->address()->save($dataArray);
        else
            $user->address->save($dataArray);

        return json(new SuccessMessage(), 201);
    }
}