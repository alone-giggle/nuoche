<?php
/**
 * 需要登陆但是不用Auth检查
 * User: xiucai
 * Date: 2018/5/16
 * Time: 15:19
 */

namespace app\admin\controller;

use think\Controller;
use think\facade\Request;
use think\facade\Response;
use think\facade\Config;
use PHPTool\NodeTree;

class NotAuth extends Controller
{
    protected $userInfo;
    protected $urlParameter;

    public function initialize()
    {
        $this->urlParameter = [
            'domain'    => Request::domain(),
            'module'    => Request::module(),
            'controller'=> Request::controller(),
            'action'    => Request::action(),
            'path'      => Request::module() . '/' . Request::controller() . '/' . Request::action(),
        ];
        $this->getUserInfo();
        $authConf = Config::pull('auth');
        if(isset($authConf['auth_menu']) && $authConf['auth_menu']) {
            $this->assignRuleTree();
        } else {
            $this->assignUserRuleTree();
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
     * 获取登陆用户信息
     */
    public function getUserInfo() {
        $this->userInfo = session('user_info');
        //if(empty($this->userInfo)) $this->redirect('admin/login/index');
        if(empty($this->userInfo)) $this->redirect(url('login/index'));
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
     * 注销登陆
     */
    public function loginout() {
        session('rule_tree', null);
        session('user_info', null);
        $this->resultData('$_0');
    }
}