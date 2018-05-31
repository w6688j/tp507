<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/30
 * Time: 22:18
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use app\api\validate\TestValidate;
use think\Exception;
use think\Validate;

class Banner
{
    /*验证的两种方法*/
    /*$data = [
        'name'  => 'vendor11111',
        'email' => 'vendor@qq',
    ];*/

    /*独立验证*/
    /*$validate = new Validate([
        'name'  => 'require|max:10',
        'email' => 'email',
    ]);*/

    /*验证器*/
    /*$validate = new TestValidate();

    $result = $validate->batch()->check($data);
    if (!$result) {
        var_dump($validate->getError());
    }*/

    /**
     * getBanner 获取指定id的Banner信息
     *
     * @url    /banner/:id
     * @http   GET
     *
     * @param int $id Banner id号
     *
     * @author wangjian
     * @time   2018/5/30 22:19
     * @throws Exception
     */
    public function getBanner($id)
    {
        (new IDMustBePostiveInt())->goCheck();
    }
}