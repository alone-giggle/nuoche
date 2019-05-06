<?php
/**
 * User: William
 * Date: 2018-03-21
 * Time: 15:42
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\model\AuthUser;
use app\admin\model\AuthGroup;

class Member extends Adminbase
{

    public function index() {
        if(Request::isAjax()) {
            $res['count'] = AuthUser::getCount();
            $res['data'] = AuthUser::getList('id,username,status,remark,create_at')->all();
            $res['code'] = 0;
            $res['msg'] = '';
            echo json_encode($res);
            exit;
        }

        return $this->fetch('memberlist');
    }

    public function add() {
        if(Request::isPost()) {
            $username = input('username');
            $password = input('password');
            $repassword = input('repassword');

            if(empty($username) || empty($password)) {
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
                'username'  => $username,
                'password'  => md5($password.$salt),
                'salt'      => $salt,
                'status'    => 1,
                'remark'    => input('remark'),
                'create_at' => date('Y-m-d H:i:s')
            ];
            if(!empty(AuthUser::getUser(['username'=>$username]))) {
                $this->resultData('$_202');
            }
            if(AuthUser::addUser($data)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            return $this->fetch('addmember');
        }
    }

    public function edit() {
        if(Request::isPost()) {
            $uid = input('uid');
            $username = input('username');

            if(empty($uid) || empty($username)) {
                $this->resultData('$_103');
            }
            $data = [
                'username'  => $username,
                'status'    => 1,
                'remark'    => input('remark'),
            ];
            if(AuthUser::updateUser($uid, $data)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            $uid = input('uid');
            $memberInfo = AuthUser::getUser(['id'=>$uid]);
            if(empty($memberInfo)) {
                return '管理员不存在';
            }

            $this->assign('memberInfo', $memberInfo);
            return $this->fetch('editmember');
        }
    }



    public function grant() {
        if(Request::isPost()) {
            $uid = input('post.uid');
            $group = input('post.group/a', []);
            if(AuthUser::updateGroupOfUser($uid, $group)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            $uid = input('uid');
            $groupList = AuthGroup::getAllGroup();
            $memberGroup = AuthUser::getGroupOfUser($uid);
            $memberGroup = array_column($memberGroup, 'id');

            $this->assign('uid', $uid);
            $this->assign('groupList', $groupList);
            $this->assign('memberGroup', $memberGroup);

            return $this->fetch('grant');
        }
    }
}