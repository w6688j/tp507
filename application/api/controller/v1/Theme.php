<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/3
 * Time: 14:03
 */

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     * getSimpleList
     *
     * @url    /theme?ids=id1,id2,id3,......
     * @http   GET
     *
     * @param string $ids ids参数
     *
     * @author wangjian
     * @time   2018/6/3 14:29
     * @return mixed
     * @throws \think\Exception
     */
    public function getSimpleList($ids = '')
    {
        (new IDCollection())->goCheck();
        $ids    = explode(',', $ids);
        $result = ThemeModel::getThemeListByIDs($ids);
        if (!$result) {
            throw new ThemeException();
        }

        return $result;
    }

    /**
     * getComplexOne 根据ID获取主题详情
     *
     * @url    /theme/:id
     * @http   GET
     *
     * @param int $id 主题id
     *
     * @author wangjian
     * @time   2018/6/3 17:40
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\Exception
     */
    public function getComplexOne($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $result = ThemeModel::getThemeWithProducts($id);
        if (!$result) {
            throw new ThemeException();
        }

        return $result;
    }
}