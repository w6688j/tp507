<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 10:09
 */

namespace app\api\validate;

use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * goCheck 验证
     *
     * @author wangjian
     * @time   2018/5/31 10:16
     * @return bool
     * @throws Exception
     */
    public function goCheck()
    {
        // 获取http传入参数
        $request = Request::instance();
        $params  = $request->param();

        // 对参数校验
        $result = $this->check($params);
        if (!$result) {
            $error = $this->getError();
            throw new Exception($error);
        } else {
            return true;
        }
    }
}