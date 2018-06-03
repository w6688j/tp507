<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    const IMG_FROM_LOCALHOST = 1;//来自本地
    const IMG_FROM_REMOTE = 2;//来自公网

    /**
     * prefixImgUrl 处理图片URL
     *
     * @param string $value 值
     * @param array  $data  记录其它信息
     *
     * @author wangjian
     * @time   2018/6/1 16:18
     *
     * @return string
     */
    protected function prefixImgUrl($value, $data)
    {
        $finalUrl = $value;
        if ($data['from'] == self::IMG_FROM_LOCALHOST)
            $finalUrl = config('setting.img_prefix') . $value;

        return $finalUrl;
    }
}
