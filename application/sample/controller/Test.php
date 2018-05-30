<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/5/29
 * Time: 13:30
 */

namespace app\sample\controller;

use think\Request;

class Test
{

    /**
     * hello 依赖注入
     *
     * @param Request $request
     *
     * @author wangjian
     * @time   2018/5/30 19:04
     * @return string
     */
    public function hello(Request $request)
    {
        $all = $request->param();

        return json_encode([
            'status' => 0,
            'msg'    => [
                'id'   => $all['id'],
                'from' => $all['from'],
                'name' => $all['name'],
                'age'  => $all['age'],
            ],
        ]);
    }


    /*获取参数的三种方法*/

    /**
     * hello1 方法一：形式参数
     *
     * @param int    $id   id
     * @param string $from 来源
     * @param string $name 姓名
     * @param int    $age  年龄
     *
     * @author wangjian
     * @time   2018/5/30 18:48
     *
     * @return string
     */
    public function hello1($id, $from, $name, $age)
    {
        return json_encode([
            'status' => 0,
            'msg'    => [
                'id'   => $id,
                'from' => $from,
                'name' => $name,
                'age'  => $age,
            ],
        ]);
    }

    /**
     * hello2 方法二：Request类
     *
     * @author wangjian
     * @time   2018/5/30 18:48
     * @return string
     */
    public function hello2()
    {
        $request = Request::instance()->param();
        $id      = Request::instance()->route('id'); //获取url路径中的参数，获取不到问号后的参数
        $name    = Request::instance()->get('name'); //获取问号后的参数，获取不到路由中定义的参数
        $age     = Request::instance()->post('age');

        //$id = Request::instance()->param('id');
        //$request = Request::instance()->get(); //获取所有url路径中的参数，获取不到问号后的参数
        //$request = Request::instance()->post(); //获取所有POST参数

        return json_encode([
            'status' => 0,
            'msg'    => [
                'id'   => $id,
                'from' => $request['from'],
                'name' => $name,
                'age'  => $age,
            ],
        ]);
    }

    /**
     * hello3 方法三：input()助手函数
     *
     * @author wangjian
     * @time   2018/5/30 18:50
     */
    public function hello3()
    {
        $request = input('param.');
        $name    = input('get.name');
        $age     = input('post.age');

        return json_encode([
            'status' => 0,
            'msg'    => [
                'id'   => $request['id'],
                'from' => $request['from'],
                'name' => $name,
                'age'  => $age,
            ],
        ]);
    }
}