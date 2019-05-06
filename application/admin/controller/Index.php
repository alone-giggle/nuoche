<?php
namespace app\admin\controller;


class Index extends NotAuth
{
    public function index()
    {
        return $this->fetch();
    }

    public function welcome() {
        $sys_info = [
            'PHP版本'     => PHP_VERSION,
            'ZEND版本'    => zend_version(),
            'MySQL版本'   => db()->query('select version() version')[0]['version'],
            '域名'        => $_SERVER['SERVER_NAME'],
            '端口'        => $_SERVER['SERVER_PORT'],
            'IP'          => $_SERVER['SERVER_ADDR'],
            'WEB服务'     => $_SERVER['SERVER_SOFTWARE'],
            '操作系统'    => PHP_OS,
            'MySQL最大连接数'    => db()->query("show variables like '%max_connections%'")[0]['Value'],
            '文件上传大小'=> get_cfg_var ("upload_max_filesize") ?: "不允许上传附件",
            '最大执行时间'=> get_cfg_var("max_execution_time") ? get_cfg_var("max_execution_time")."秒": "无限制",
            '系统当前时间'=> date("Y-m-d H:i:s"),
        ];

        $this->assign('sys_info', $sys_info);
        return $this->fetch('welcome');
    }

    public function editPassword() {
        $uid = input('uid');
        if($this->userInfo['id'] != $uid && $this->userInfo['super'] != 1) {
            return '无权操作请联系超级管理';
        }
        if(\Think\Facade\Request::isPost()) {
            $uid = input('uid');
            $username = input('username');
            $password = input('password');
            $repassword = input('repassword');

            if(empty($uid) || empty($username) || empty($password)) {
                $this->resultData('$_103');
            }
            if(strlen($password) < 6) {
                $this->resultData('$_201');
            }
            if($password != $repassword) {
                $this->resultData('$_200');
            }
            $salt = randStr(6);
            $data = [
				'username'	=> $this->userInfo['username'] != 'guest' ? $username : 'guest',
//                'password'  => md5($password.$salt),
                'password'  => $this->userInfo['super'] != 1 ? md5(md5('123456').$salt) : md5($password.$salt),
                'salt'      => $salt,
                'status'    => 1,
            ];
			
            if(\app\admin\model\AuthUser::updateUser($uid, $data)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            $memberInfo = \app\admin\model\AuthUser::getUser(['id'=>$uid]);
            if(empty($memberInfo)) {
                return '管理员不存在';
            }

            $this->assign('memberInfo', $memberInfo);
            return $this->fetch('member/editpassword');
        }
    }

    public function diy() {
        return '此功能需要你根据业务场景具体开发';
    }
}
