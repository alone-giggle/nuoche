<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/18
 * Time: 13:44
 */

namespace app\admin\model;

use think\Model;

class AuthRule extends Model
{
    /**
     * 获取访问列表
     * @return array
     */
    public static function getRuleList() {
        $res = self::order('sort', 'asc')->select()->toArray();
        return $res;
    }

    /**
     * 添加访问规则
     * @param array $data
     * @return int|string
     */
    public static function addMenu($data=[]) {
        if(isset($data['parent_id']) && $data['parent_id']) {
            return self::where('parent_id', $data['parent_id'])->insert($data);
        } else {
            return self::insert($data);
        }
    }

    public static function editMenu($data=[], $where=[]) {
        return self::where($where)->update($data);
    }
}