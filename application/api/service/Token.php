<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 20:09
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    /**
     * generateToken 生成随机Token
     *
     * @author wangjian
     * @time   2018/6/4 20:26
     * @return string
     */
    public static function generateToken()
    {
        // 32个字符组成一组随机字符串
        $randChars = getRandChars();
        // 用三组字符串进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = config('secure.token_salt');

        return md5($randChars . $timestamp . $salt);
    }

    /**
     * getCurrentTokenVar 获取当前Token对应值
     *
     * @param string $key 键名
     *
     * @author wangjian
     * @time   2018/6/9 17:41
     *
     * @return mixed
     * @throws TokenException
     * @throws \think\Exception
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()
            ->header('token');

        $vars = Cache::get($token);
        if (!$vars)
            throw new TokenException();

        if (!is_array($vars))
            $vars = json_decode($vars, true);

        if (!array_key_exists($key, $vars))
            throw new Exception('尝试获取的Token变量并不存在');

        return $vars[$key];
    }

    /**
     * getCurrentUID 获取当前UID
     *
     * @author wangjian
     * @time   2018/6/9 17:44
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUID()
    {
        $uid = self::getCurrentTokenVar('uid');

        return $uid;
    }

    /**
     * getCurrentScope 获取当前权限值
     *
     * @author wangjian
     * @time   2018/6/11 11:09
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentScope()
    {
        $scope = self::getCurrentTokenVar('scope');

        return $scope;
    }

    /**
     * needPrimaryScope 需要用户和CMS管理员都可以访问的权限
     *
     * @author wangjian
     * @time   2018/6/14 22:41
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope()
    {
        // 根据Token获取用户Scope
        $scope = self::getCurrentScope();
        if (!$scope) {
            throw new TokenException();
        }
        if ($scope < ScopeEnum::User) {
            throw new ForbiddenException();
        }

        return true;
    }

    /**
     * needExclusiveScope 只有用户可以访问的权限
     *
     * @author wangjian
     * @time   2018/6/11 11:14
     * @return bool
     * @throws ForbiddenException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public static function needExclusiveScope()
    {
        // 根据Token获取用户Scope
        $scope = self::getCurrentScope();
        if (!$scope) {
            throw new TokenException();
        }
        if ($scope != ScopeEnum::User) {
            throw new ForbiddenException();
        }

        return true;
    }
}