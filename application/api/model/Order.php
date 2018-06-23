<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/20
 * Time: 12:55
 */

namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    /**
     * getSummaryByUser 分页获取订单列表
     *
     * @param int $uid  用户id
     * @param int $page 当前页
     * @param int $size 分页大小
     *
     * @author wangjian
     * @time   2018/6/23 19:55
     *
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        return (new self())
            ->where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
    }
}