<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 8:42
 */

namespace app\api\model;

class Category extends BaseModel
{
    protected $hidden = [
        'topic_img_id',
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
     * getCategories 获取分类列表
     *
     * @author wangjian
     * @time   2018/6/4 8:45
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategories()
    {
        return ((new self())->all([], ['topicImg']));
    }
}