<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/14
 * Time: 22:47
 */

namespace app\api\controller;

use app\api\service\Token as TokenService;
use app\lib\exception\ForbiddenException;
use think\Controller;

class BaseController extends Controller
{
    /**
     * checkPrimaryScope 检查用户和管理员权限
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
        return TokenService::needPrimaryScope();
    }

    /**
     * checkExclusiveScope 检查用户权限
     *
     * @author wangjian
     * @time   2018/6/14 22:55
     * @return bool
     * @throws ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    protected function checkExclusiveScope()
    {
        return TokenService::needExclusiveScope();
    }

}