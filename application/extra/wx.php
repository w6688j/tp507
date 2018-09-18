<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 10:32
 */

return [
    'app_id'           => 'wx8390c959e24ce6d0',
    'app_secret'       => 'e0f1ca4aa10c8b6dd914882eb92200bd',
    'login_url'        => 'https://api.weixin.qq.com/sns/jscode2session?' .
        'appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",
];