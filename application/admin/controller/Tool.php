<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/10
 * Time: 14:57
 */

namespace app\admin\controller;

use think\Controller;
use think\captcha\Captcha;

class Tool extends Controller
{
    public function captcha() {
        $config = [
            'fontSize' => 18,
            // 验证码图片高度
            'imageH'   => 38,
            // 验证码图片宽度
            'imageW'   => 130,
            // 验证码位数
            'length'   => 4,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}