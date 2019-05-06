<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/10
 * Time: 14:03
 */

namespace app\admin\controller;

use think\facade\Request;
use think\facade\Response;
use think\facade\Config;
use think\Controller;
use PHPTool\Auth;
use PHPTool\NodeTree;

class Adminbase extends Controller
{
    protected $userInfo;
    protected $urlParameter;

    public function initialize() {
        $this->urlParameter = [
            'domain'    => Request::domain(),
            'module'    => Request::module(),
            'controller'=> Request::controller(),
            'action'    => Request::action(),
            'path'      => Request::module() . '/' . Request::controller() . '/' . Request::action(),
        ];
        // 完整的url中path部分
        $path = strtolower($this->urlParameter['path']);
        // 登陆链接的path
        $login_path = 'admin/login/index';
        if($path == $login_path) {
            return true;
        }
        $this->getUserInfo();
        $this->authCheck();
        $this->specialActionCheck();
        $authConf = Config::pull('auth');
        if(isset($authConf['auth_menu']) && $authConf['auth_menu']) {
            $this->assignRuleTree();
        } else {
            $this->assignUserRuleTree();
        }
    }

    /**
     * 获取登陆用户信息
     */
    public function getUserInfo() {
        $this->userInfo = session('user_info');
        //if(empty($this->userInfo)) $this->redirect('login/index');
        if(empty($this->userInfo)) $this->redirect(url('admin/login/index'));

        $this->assign('userInfo', $this->userInfo);
    }

    /**
     * 用户拥有的权限数组分配给模板
     * @param array $ruleTree
     */
    public function assignUserRuleTree() {
        $ruleTree = [];
        $uid = $this->userInfo['id'];
        $this->assign('rule_tree', $ruleTree);
    }

    /**
     * 分配所有权限数组给模板
     */
    public function assignRuleTree() {
        $rule_list = \app\admin\model\AuthRule::getRuleList();
        $rule_tree = NodeTree::makeTree($rule_list);
        $this->assign('rule_tree', $rule_tree);
    }

    /**
     * 权限检查
     * @return bool
     */
    public function authCheck() {
        if($this->userInfo['super']) {
            return true;
        }
        $auth = new Auth();
        if($auth->check(strtolower($this->urlParameter['path']), $this->userInfo['id'])) {
            return true;
        } else {
            if(Request::instance()->isAjax()) {
                $this->resultData('$_501');
            }
            exit("无权操作!");
        }
    }
    /**
     * 格式化返回数据
     * @param array $data   数据
     * @param array $code   错误代码数组
     * @return mixed
     */
    public function resultData($code = ["0", "操作成功"], $data = [], $type = 'array'){
        if (is_string($code)) {
            @eval('$code = \\extend\\Statuscode::' . $code . ';');
        }
        $result['cmd'] = $this->urlParameter['path'];    //接口名称
        $result['errCode'] = $code[0];
        $result['msg'] = $code[1];
        if ($type == 'array' && !is_array($data)) {
            $data = [];
        } elseif ($type == 'string') {
            $result['msg'] .=', '. $data;
            $data = [];
        } elseif (!in_array($type, ['array', 'string'])) {
            $data = [];
        }
        $result['detail'] = $data;

        Response::create($result,'json')->send();
        exit;
    }

    /**
     * 检验指定参数是否存在或有效
     * @param array $data
     */
    protected function checkParams($data = []) {
        $arr = func_get_args();
        if (count($arr) == 1) {
            $arr = (array) $data;
        }
        foreach ($arr as $v) {
            if (strpos($v, '|') === false) {
                $name = $v;
                $filter = '';
            } else {
                list($name, $filter) = explode('|', $v);
            }
            $tmp = input('param.' . $name, '', $filter);
            if (!$tmp) {
                return false;
            }
        }
        return true;
    }

    /**
     * 特殊行为检查
     * @return string
     */
    protected function specialActionCheck() {
        // 特殊行为集合,注意都是小写
        $specialController = [
            'admin/member/add','admin/member/edit','admin/member/grant',
            'admin/group/add','admin/group/edit','admin/group/grantauth',
            'admin/system/addmenu','admin/system/editmenu','admin/system/delmenu',
        ];
        if(in_array(strtolower($this->urlParameter['path']), $specialController)) {
            if($this->userInfo['super'] != 1) {
                if(Request::instance()->isAjax()) {
                  $this->resultData('$_500');
                }
                exit('无权操作请联系超级管理');
            }
        }
    }
}