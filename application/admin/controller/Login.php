<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/10
 * Time: 14:06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\model\AuthUser;

class Login extends Adminbase
{
    public function index() {
        if($this->request->isPost()) {
            $username = Request::post('userName');
            $password = Request::post('password');
            $captcha = Request::post('captcha');
            $remember = Request::post('rememberMe');

            if(!captcha_check($captcha)) {
                $this->resultData('$_2');
            }
            $user_info = AuthUser::login($username, $password);
            if($user_info) {
                if($remember) {
                    session([
                        'expire'     => 86400,
                    ]);
                }
                $user_session_info = [
                    'id'        => $user_info['id'],
                    'username'  => $user_info['username'],
                    'super'     => $user_info['super'],
                ];
                session('user_info', $user_session_info);
                $this->resultData('$_0');
            }
            $this->resultData('$_3');
        }
        $userInfo = session('user_info');
        if($userInfo) {
            $this->redirect('index/index');
        }
        return $this->fetch('index');
    }

}