<?php

namespace app\api\model;

class BannerItem extends BaseModel
{
    /**
     * @var array  隐藏字段
     */
    protected $hidden = [
        'id',
        'img_id',
        'banner_id',
        'delete_time',
        'update_time',
    ];

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
