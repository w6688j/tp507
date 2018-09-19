<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/9/19
 * Time: 13:46
 */

namespace app\api\controller\v1\admin;

use app\api\controller\BaseController;

class Navlist extends BaseController
{
    public function getAll()
    {
        return json([
            'code' => 0,
            'data' => [
                [
                    'id'   => '1',
                    'href' => 'index',
                    'text' => '管理',
                    'list' => [
                        ['id' => 1, 'name' => '商品管理', 'list' => $this->getProductOperation()],
                        ['id' => 2, 'name' => '订单管理', 'list' => $this->getOrderOperation()],
                        ['id' => 3, 'name' => '用户管理', 'list' => $this->getUserOperation()],
                    ],
                ],
                [
                    'id'   => '99',
                    'href' => 'system/permission',
                    'text' => '系统',
                    'list' => [
                        [
                            'id'   => 1,
                            'text' => '权限列表',
                            'href' => 'system/permission',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * getProductOperation 获取商品操作菜单
     *
     * @author wangjian
     * @time   2018/9/19 16:17
     * @return array
     */
    private function getProductOperation()
    {
        return [
            [
                'id'            => 11,
                'text'          => '产品列表',
                'href'          => 'product',
                'hasPermission' => true,
            ],
        ];
    }

    /**
     * getOrderOperation 获取订单操作菜单
     *
     * @author wangjian
     * @time   2018/9/19 16:20
     * @return array
     */
    private function getOrderOperation()
    {
        return [
            [
                'id'            => 21,
                'text'          => '订单列表',
                'href'          => 'order',
                'hasPermission' => true,
            ],
        ];
    }

    /**
     * getUserOperation 获取用户操作菜单
     *
     * @author wangjian
     * @time   2018/9/19 16:20
     * @return array
     */
    private function getUserOperation()
    {
        return [
            [
                'id'            => 31,
                'text'          => '用户列表',
                'href'          => 'user',
                'hasPermission' => true,
            ],
        ];
    }
}