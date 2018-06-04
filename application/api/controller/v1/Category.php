<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/6/4
 * Time: 8:41
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    /**
     * getAllCategories 获取分类列表
     *
     * @url    /category/all
     * @http   GET
     *
     * @author wangjian
     * @time   2018/6/4 8:50
     * @return false|\PDOStatement|string|\think\Collection
     * @throws CategoryException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllCategories()
    {
        $categories = CategoryModel::getCategories();
        if ($categories->isEmpty()) {
            throw new CategoryException();
        }

        return $categories;
    }
}