<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/4/12
 * Time: 21:00
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\model\AuthGroup;

class Group extends Adminbase
{
    public function index() {
        if(Request::isAjax()) {
            $datas = AuthGroup::getList();
            $this->assign('datas', $datas);
            $res['count'] = AuthGroup::getCount();
            $res['data'] = AuthGroup::getList()->all();
            $res['code'] = 0;
            $res['msg'] = '';
            echo json_encode($res);
            exit;
        }

        return $this->fetch('grouplist');
    }

    public function add() {
        if(Request::isPost()) {
            $title = input('title');
            $status = input('status');

            if(empty($title)) {
                $this->resultData('$_100');
            }
            $data = [
                'title' => $title,
                'status'=> $status
            ];
            if(AuthGroup::addAuthGroup($data)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        }else{
            return $this->fetch('addgroup');
        }
    }

    public function edit() {
        if(Request::isPost()) {
            $title = input('title');
            $status = input('status');
            $gid = input('gid');

            if(empty($title) || empty($gid)) {
                $this->resultData('$_100');
            }
            $data = [
                'title' => $title,
                'status'=> $status
            ];
            if(AuthGroup::updateAuthGroup($gid, $data)) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            $gid = input('gid', 0);
            $gInfo = AuthGroup::getAuthGroupInfo($gid);

            $this->assign('gid', $gid);
            $this->assign('gInfo', $gInfo);

            return $this->fetch('editgroup');
        }
    }

    public function grantAuth() {
        if(Request::isPost()) {
            $gid = input('gid', 0);
            $rules = input('rules', '');

            if(AuthGroup::updateAuthGroup($gid, ['rules'=>$rules])) {
                $this->resultData('$_0');
            } else {
                $this->resultData('$_1');
            }
        } else {
            $gid = input('gid', 0);
            $rules = AuthGroup::getRule($gid);
            foreach ($rules as $key => $value) {
                $rules[$key]['open'] = true;
            }
            $this->assign('gid', $gid);
            $this->assign('rules', json_encode($rules));

            return $this->fetch('grant');
        }
    }
}