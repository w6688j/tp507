<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/30
 * Time: 22:18
 */

namespace app\api\controller\v2;

use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;
use think\Exception;

class Banner
{
    /**
     * getBanner 获取指定id的Banner信息
     *
     * @url    /banner/:id
     * @http   GET
     *
     * @param int $id Banner id号
     *
     * @author wangjian
     * @time   2018/5/30 22:19
     * @return string
     * @throws Exception
     */
    public function getBanner($id)
    {

        return 'This is v2 Version';
    }
}