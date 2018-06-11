<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/9
 * Time: 17:09
 */

namespace app\api\controller\v1;

use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Controller;

class Address extends Controller
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress'],
    ];

    /**
     * checkPrimaryScope 检查初级权限
     *
     * @author wangjian
     * @time   2018/6/11 11:14
     * @return bool
     * @throws ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    protected function checkPrimaryScope()
    {
        // 根据Token获取用户Scope
        $scope = TokenService::getCurrentScope();
        if (!$scope) {
            throw new TokenException();
        }
        if ($scope < ScopeEnum::User) {
            throw new ForbiddenException();
        }

        return true;
    }

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