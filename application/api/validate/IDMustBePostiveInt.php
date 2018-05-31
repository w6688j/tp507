<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 9:41
 */

namespace app\api\validate;

class IDMustBePostiveInt extends BaseValidate
{
    protected $rule = [
        'id'  => 'require|isPostiveInteger',
        'num' => 'in:1,2,3',
    ];

    /**
     * isPostiveInteger 验证是否是正整数
     *
     * @param mixed  $value 值
     * @param string $rule  规则
     * @param string $data  数据
     * @param string $field 字段
     *
     * @author wangjian
     * @time   2018/5/31 9:44
     * @return bool|string
     */
    protected function isPostiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return $field . '必须是正整数';
        }
    }
}