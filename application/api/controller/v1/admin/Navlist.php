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
                    'id'   => '2',
                    'href' => 'index',
                    'text' => '报表',
                    'list' => [
                        ['id' => 1, 'name' => '门店报表', 'list' => $this->storeReport()],
                        ['id' => 2, 'name' => '财务报表', 'list' => $this->storeReport()],
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
     * storeReport 门店报表
     *
     * @author chenchao
     * @return array
     */
    private function storeReport()
    {
        return [
            [
                'id'            => 21,
                'text'          => 'finance',
                'href'          => 'finance',
                'hasPermission' => true,
            ],
            [
                'id'            => 22,
                'text'          => 'finance',
                'href'          => 'finance',
                'hasPermission' => true,
            ],
        ];
    }
}