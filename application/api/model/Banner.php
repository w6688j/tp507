<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/31
 * Time: 11:47
 */

namespace app\api\model;

use think\Model;

class Banner extends Model
{
    /**
     * @var array  隐藏字段
     */
    protected $hidden = [
        'delete_time',
        'update_time',
    ];

    /**
     * getBannerByID 根据ID获取banner信息
     *
     * @param int $id
     *
     * @author wangjian
     * @time   2018/5/31 16:11
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @return mixed
     */
    static public function getBannerByID($id)
    {
        /*操作数据库的三种方法*/
        //方法一：原生SQL
        //$result = Db::query('select * from banner_item where banner_id=?', [$id]);

        //方法二：构造器
        //where三种方法：表达式、数组法、闭包
        //表达式 where('字段名','表达式','查询条件')
        //数组法 不推荐使用
        //闭包   where(function($query) use ($id){
        //          $query->where('banner_id', '=', $id)
        //      })
        /*$result = Db::table('banner_item')
            ->where('banner_id', '=', $id)
            ->select();*/

        //方法三：ORM 对象关系映射
        return (new self())->with(['items', 'items.img'])->find($id);
    }

    /**
     * items 关联模型
     *
     * @author wangjian
     * @time   2018/6/1 10:07
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }
}