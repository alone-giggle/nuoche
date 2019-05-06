<?php


namespace app\m\controller;


use think\Controller;
use think\Db;

class Car extends Controller
{

    /**
     * 扫码入口
     * @return string|\think\response\Redirect|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $rand_code = request()->route('id');

        $info = Db::name('UserCar')->where('rand_code', $rand_code)->find();
        if (!is_array($info))
        {
            //没有绑定过
            return redirect('/car/bind/'.$rand_code.'.html');
        }

        return $this->fetch();
    }


    public function bind()
    {
        echo '11';
    }


}