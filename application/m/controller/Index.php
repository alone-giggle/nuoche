<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 15:44
 */

namespace app\m\controller;

use think\facade\Config;
class Index
{
    public function index()
    {
        dump(Config::get());
    }
}