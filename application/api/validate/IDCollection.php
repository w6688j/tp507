<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/3
 * Time: 14:31
 */

namespace app\api\validate;

class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs',
    ];

    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数',
    ];

    /**
     * checkIDs 验证ids
     *
     * @param mixed $value 值
     *
     * @author wangjian
     * @time   2018/6/3 14:40
     *
     * @return bool
     */
    protected function checkIDs($value)
    {
        $values = explode(',', $value);
        if (empty($values))
            return false;
        foreach ($values as $id) {
            if (!$this->isPositiveInteger($id))
                return false;
        }

        return true;
    }
}