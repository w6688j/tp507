<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 10:18
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    /**
     * UserToken constructor.
     *
     * @param string $code 微信登录时获取的code
     */
    public function __construct($code)
    {
        $this->code        = $code;
        $this->wxAppID     = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl  = sprintf(
            config('wx.login_url'),
            $this->wxAppID,
            $this->wxAppSecret,
            $this->code
        );
    }

    /**
     * get 请求微信服务器，获取Token
     *
     * @author wangjian
     * @time   2018/6/4 11:42
     * @throws Exception
     * @throws WeChatException
     * @return mixed
     */
    public function get()
    {
        $result   = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult))
            throw new Exception('获取session_key及open_id时异常，微信内部错误');

        if (array_key_exists('errcode', $wxResult))
            $this->processLoginError($wxResult);

        return $this->grantToken($wxResult);
    }

    /**
     * grantToken 授权Token
     *
     * @param array $wxResult 微信返回数组
     *
     * @return string
     * @throws Exception
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author wangjian
     * @time   2018/6/4 19:52
     *
     */
    private function grantToken($wxResult)
    {
        // 拿到openID
        $openid = $wxResult['openid'];
        // 数据库查询此openID是否存在
        $user = UserModel::getByOpenId($openid);
        // 如果存在则不处理，如果不存在则新增记录
        if ($user)
            $uid = $user->id;
        else
            $uid = $this->newUser($openid);
        // 生成令牌，准备缓存数据，写入缓存 key:令牌 value:wxResult,uid,scope(决定用户身份权限)
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);

        // 返回令牌
        $token = $this->saveToCache($cachedValue);

        return $token;
    }

    /**
     * newUser 创建用户记录
     *
     * @param string $openid 微信用户openId
     *
     * @author wangjian
     * @time   2018/6/4 19:55
     *
     * @return int
     */
    private function newUser($openid)
    {
        $user = UserModel::create([
            'openid' => $openid,
        ]);

        return $user->id;
    }

    /**
     * prepareCachedValue 组装Cache数据
     *
     * @param array $wxResult 微信返回数组
     * @param int   $uid      用户id
     *
     * @author wangjian
     * @time   2018/6/4 20:04
     *
     * @return array
     */
    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue        = $wxResult;
        $cachedValue['uid'] = $uid;
        // scope 16:代表App用户的权限值 32:代表CMS（管理员）用户的权限值
        $cachedValue['scope'] = ScopeEnum::User;

        return $cachedValue;
    }

    /**
     * saveToCache Token写入缓存
     *
     * @param array $cachedValue 缓存值
     *
     * @author wangjian
     * @time   2018/6/4 20:36
     *
     * @return string
     * @throws Exception
     * @throws TokenException
     */
    private function saveToCache($cachedValue)
    {
        // 生成Token
        $key       = self::generateToken();
        $value     = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');

        // 写入缓存
        $request = cache($key, $value, $expire_in);
        if (!$request) {
            throw new TokenException([
                'msg'       => '服务器缓存异常',
                'errorCode' => 10005,
            ]);
        }

        return $key;
    }

    /**
     * processLoginError 处理错误
     *
     * @param array $wxResult 微信返回结果数组
     *
     * @author wangjian
     * @time   2018/6/4 11:41
     *
     * @throws Exception
     * @throws WeChatException
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatException([
            'msg'       => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode'],
        ]);
    }
}