<?php

namespace app\api\model;

use think\Model;

class BannerItem extends Model
{
    /**
     * img 关联模型
     *
     * @author wangjian
     * @time   2018/6/1 10:38
     * @return \think\model\relation\BelongsTo
     */
    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
