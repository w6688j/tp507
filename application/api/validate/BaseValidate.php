<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 10:09
 */

namespace app\api\validate;

use app\lib\exception\ParameterException;
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
        $result = $this->batch()->check($params);
        if (!$result) {
            throw new ParameterException([
                'msg' => $this->getError(),
            ]);
        } else {
            return true;
        }
    }

    /**
     * isPositiveInteger 验证是否是正整数
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
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        return (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0);
    }

    /**
     * isNotEmpty 判断是否为空
     *
     * @param mixed  $value 值
     * @param string $rule  规则
     * @param string $data  数据
     * @param string $field 字段
     *
     * @author wangjian
     * @time   2018/6/4 10:03
     *
     * @return bool
     */
    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        return !empty($value);
    }

    /**
     * isMobile 判断是否是手机号
     *
     * @param mixed  $value 值
     * @param string $rule  规则
     * @param string $data  数据
     * @param string $field 字段
     *
     * @author wangjian
     * @time   2018/6/9 19:01
     *
     * @return bool
     */
    protected function isMobile($value, $rule = '', $data = '', $field = '')
    {
        $rule   = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);

        return (bool)$result;
    }

    /**
     * getDataByRule 根据验证规则过滤参数
     *
     * @param array $params 参数
     *
     * @author wangjian
     * @time   2018/6/9 18:42
     *
     * @return array
     * @throws Exception
     * @throws ParameterException
     */
    public function getDataByRule($params)
    {
        if (array_key_exists('user_id', $params) || array_key_exists('uid', $params))
            throw new ParameterException([
                'msg' => '参数包含有非法的参数名user_id或uid',
            ]);

        $data = [];
        foreach ($this->rule as $key => $value) {
            $data[$key] = $params[$key];
        }

        return $data;
    }
}