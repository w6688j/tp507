<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 9:56
 */

namespace app\api\controller\v1;

use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\service\Token as TokenService;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;

class Token
{
    /**
     * getToken 获取Token
     *
     * @url    /token/user
     * @http   POST
     *
     * @param string $code 微信登录时获取的code
     *
     * @author wangjian
     * @time   2018/6/4 9:58
     * @return array
     * @throws \think\Exception
     */
    public function getToken($code = '')
    {
        (new TokenGet())->goCheck();

        return [
            'token' => (new UserToken($code))->get(),
        ];
    }

    /**
     * 第三方应用获取令牌
     *
     * @url  /app_token?
     * @POST ac=:ac se=:secret
     * @throws \think\Exception
     */
    public function getAppToken($ac = '', $se = '')
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');
        (new AppTokenGet())->goCheck();
        $app   = new AppToken();
        $token = $app->get($ac, $se);

        return [
            'token' => $token,
        ];
    }

    /**
     * verifyToken
     *
     * @param string $token
     *
     * @throws ParameterException
     * @throws \think\Exception
     * @return array
     * @author wangjian
     * @time   2018/9/18 17:25
     */
    public function verifyToken($token = '')
    {
        if (!$token) {
            throw new ParameterException([
                'token不允许为空',
            ]);
        }
        $valid = TokenService::verifyToken($token);

        return [
            'isValid' => $valid,
        ];
    }
}