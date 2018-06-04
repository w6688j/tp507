<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 20:09
 */

namespace app\api\service;

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
}