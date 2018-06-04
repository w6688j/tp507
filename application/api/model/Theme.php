<?php

namespace app\api\model;

class Theme extends BaseModel
{
    protected $hidden = [
        'topic_img_id',
        'head_img_id',
        'delete_time',
        'update_time',
    ];

    /**
     * topicImg 关联模型
     *
     * @author wangjian
     * @time   2018/6/3 14:12
     * @return \think\model\relation\BelongsTo
     */
    public function topicImg()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    /**
     * headImg 关联模型
     *
     * @author wangjian
     * @time   2018/6/3 14:12
     * @return \think\model\relation\BelongsTo
     */
    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    /**
     * products 关联模型
     *
     * @author wangjian
     * @time   2018/6/3 17:04
     * @return \think\model\relation\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }

    /**
     * getThemeListByIDs 根据IDs获取主题列表
     *
     * @param array $ids id数组
     *
     * @author wangjian
     * @time   2018/6/3 16:50
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @return mixed
     */
    public static function getThemeListByIDs($ids = [])
    {
        return (new self())->with(['topicImg', 'headImg'])->select($ids);
    }

    /**
     * getThemeWithProducts 获取主题与相关产品
     *
     * @param int $id 主题id
     *
     * @author wangjian
     * @time   2018/6/3 17:43
     *
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getThemeWithProducts($id)
    {
        return ((new self())->with(['products', 'topicImg', 'headImg'])->find($id));
    }
}