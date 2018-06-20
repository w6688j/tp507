<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/15
 * Time: 7:28
 */

namespace app\api\validate;

use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'checkProducts',
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count'      => 'require|isPositiveInteger',
    ];

    /**
     * checkProducts 验证产品列表
     *
     * @param array $values 产品列表数组
     *
     * @author wangjian
     * @time   2018/6/15 8:13
     *
     * @return bool
     * @throws ParameterException
     * @throws \think\Exception
     */
    protected function checkProducts($values)
    {
        if (empty($values)) {
            throw new ParameterException([
                'msg' => '商品列表不能为空',
            ]);
        }

        if (!is_array($values)) {
            throw new ParameterException([
                'msg' => '商品参数不正确',
            ]);
        }

        foreach ($values as $value) {
            $this->checkProduct($value);
        }

        return true;
    }

    /**
     * checkProduct 验证单个产品
     *
     * @param array $value 单个产品数据
     *
     * @author wangjian
     * @time   2018/6/15 8:13
     *
     * @throws ParameterException
     * @throws \think\Exception
     */
    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result   = $validate->check($value);
        if (!$result) {
            throw new ParameterException([
                'msg' => '商品列表参数不正确',
            ]);
        }
    }
}