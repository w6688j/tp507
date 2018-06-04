<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 9:56
 */

namespace app\api\controller\v1;

use app\api\service\UserToken;
use app\api\validate\TokenGet;

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
}