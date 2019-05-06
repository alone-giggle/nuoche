<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/18
 * Time: 17:52
 */

namespace app\admin\controller;

use app\admin\model\AuthRule;
use think\facade\Request;
use think\facade\Response;
use PHPTool\SelectTree;

class System extends Adminbase
{

    public function menuList() {
        if(Request::instance()->isAjax()) {
            $rule_list = AuthRule::getRuleList();
            $option = [
                'parent_key'    => 'parent_id',
                'menu_name'     => 'title',
            ];
            $tree = new SelectTree($rule_list, $option);
            $html_tree = $tree->getArray();
            $data = [
                'code'      => 0,
                'msg'       => '',
                'count'     => 1000,
                'data'      => array_values($html_tree),
            ];
            Response::create($data, 'json')->send();
            exit;
        }
        return $this->fetch('menulist');
    }

    public function addMenu() {
        if(Request::instance()->isAjax()) {
            $pid = input('post.parent', 0, 'intval');
            $update = [];
            $data = [
                'parent_id' => $pid,
                'name'      => input('post.path', '', 'strtolower'),
                'title'     => input('post.menu', ''),
                'sort'      => input('post.num', 0, 'intval'),
                'status'    => input('post.status', 0, 'intval'),
            ];
            if($pid) {
                $pdata = AuthRule::get($pid);
                if(empty($pdata)) {
                    $this->resultData('$_102');
                } else {
                    $len = count(explode(',', $pdata['pth']));
                    if ($len > 4) {
                        $this->resultData('$_5');
                    }
                }
                $update['pth'] = $pdata['pth'] . ',';
            }

            $res = AuthRule::addMenu($data);
            if($res) {
                $insert_id = AuthRule::getLastInsID();
                if($pid) {
                    $update['pth'] .= $insert_id;
                } else {
                    $update['pth'] = '0,' . $insert_id;
                }
                AuthRule::editMenu($update,['id'=>$insert_id]);
                $this->resultData('$_0');
            }
        }
        $html_tree = [];
        $parent_id = input('get.p', 0);
        if($parent_id) {
            $rule_list = AuthRule::getRuleList();
            $option = [
                'parent_key'    => 'parent_id',
                'menu_name'     => 'title',
            ];
            $tree = new SelectTree($rule_list, $option);
            $html_tree = $tree->getArray(0,0, ' ');
        }
        $this->assign('tree', $html_tree);
        $this->assign('pid', $parent_id);
        return $this->fetch('addmenu');
    }

    public function editMenu() {
        if(Request::instance()->isAjax()) {
            $id = input('post.id', 0, 'intval');
            if($id <= 0) {
                $this->resultData('$_100');
            }
            $data = [
                'name'      => input('post.path', '', 'strtolower'),
                'title'     => input('post.menu', ''),
                'sort'      => input('post.num', 0, 'intval'),
                'status'    => input('post.status', 0, 'intval'),
            ];
            $where['id'] = $id;
            $res = AuthRule::editMenu($data, $where);
            if($res === false) {
                $this->resultData('$_101');
            }
            $this->resultData('$_0');
        }
        $id = input('id');
        $data = AuthRule::get($id);

        $this->assign('data', $data);
        return $this->fetch('editmenu');
    }

    public function delMenu() {
        $id = input('post.id', 0);
        if(empty($id)) {
            $this->resultData('$_100');
        }
        $sql = "DELETE FROM think_auth_rule WHERE FIND_IN_SET($id, pth)";
        $res = AuthRule::query($sql);
        if($res === false) {
            $this->resultData('$_101');
        }
        $this->resultData('$_0');
    }
}