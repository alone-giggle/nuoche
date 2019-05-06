<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Cate;
use app\index\model\Library;
use Think\Db;
use think\facade\Request;
use app\index\model\Goods;



class Index extends Controller
{
    /**
     * 首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        return $this->fetch();
    }
}
