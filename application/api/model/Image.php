<?php

namespace app\api\model;

class Image extends BaseModel
{
    /**
     * @var array  显示字段
     */
    protected $visible = ['url'];

    /**
     * getUrlAttr URL读取器
     *
     * @param string $value 值
     * @param array  $data  记录其它信息
     *
     * @author wangjian
     * @time   2018/6/3 11:16
     *
     * @return string*
     */
    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}

