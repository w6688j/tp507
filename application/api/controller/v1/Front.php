<?php
/**
 * Created by PhpStorm.
 * User: WJ
 * Date: 2018/10/18
 * Time: 18:48
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\FrontRegister;
use app\lib\exception\SuccessMessage;

class Front extends BaseController
{
    /**
     * register
     *
     * @author wangjian
     * @time   2018/10/18 18:53
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function register()
    {
        (new FrontRegister())->goCheck();

        return json(new SuccessMessage());
    }
}