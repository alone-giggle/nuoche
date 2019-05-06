<?php


namespace app\admin\controller;


class Cache extends Adminbase
{
    /**
     * 清缓存页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 清理
     */
    public function clear() {
        $type = I('get.type','trim');
        $obj_dir = new \Dir;
        switch ($type) {
            case 'field':
                is_dir('/runtime/data') && $obj_dir->del('/runtime/data');
                break;
            case 'tpl':
                is_dir('/runtime/temp') && $obj_dir->del('/runtime/temp');
                break;
            case 'data':
                is_dir('/runtime/data') && $obj_dir->del('/runtime/data');
                break;
            case 'logs':
                is_dir('/runtime/log') && $obj_dir->del('/runtime/log');
                break;
        }
        $this->ajaxReturn(1);
    }

    /**
     * 全部清理
     */
    public function qclear() {
        $obj_dir = new \Dir;
        is_dir('/runtime/data') && $obj_dir->del('/runtime/data');
        is_dir('/runtime/temp') && $obj_dir->del('/runtime/temp');
        is_dir('/runtime/data') && $obj_dir->del('/runtime/data');
        is_dir('/runtime/log') && $obj_dir->del('/runtime/log');
        $this->ajaxReturn(1, L('clear_success'));
    }

}