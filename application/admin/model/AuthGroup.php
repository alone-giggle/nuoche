<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/4/12
 * Time: 21:06
 */

namespace app\admin\model;

use think\Model;

class AuthGroup extends Model
{
    /**
     * 角色列表
     * @param string $field
     * @param array $where
     * @return \think\Paginator
     */
    public static function getList($field='*', $where=[]) {
        $listRow = input('limit') ?: config('paginate.list_rows');
        $res = self::field($field)
            ->where(function ($query) use ($where){
            if($where) $query->where($where);
        })->paginate($listRow);
        return $res;
    }

    public static function getAllGroup() {
        $res = self::where('status', 1)->select();
        return $res;
    }
    /**
     * 角色数量统计
     * @param array $where
     * @return int|string
     */
    public static function getCount($where=[]) {
        $count = self::where(function ($query) use ($where) {
            if($where) $query->where($where);
        })->count();
        return $count;
    }

    /**
     * 角色拥有的节点
     * @param $gid
     * @return false|static[]
     */
    public static function getRule($gid) {
        // 所有节点
        $allRule = self::field('id, parent_id pId, title name')
            ->table('think_auth_rule')
            ->order('sort', 'asc')
            ->select();
        // 角色拥有的节点
        $res = self::field('rules')
            ->where('id', $gid)
            ->find();
        $groupRule = explode(',', $res['rules']);
        foreach ($allRule as $key => $rule) {
            if(in_array($rule['id'], $groupRule)) {
                $allRule[$key]['checked'] = true;
            }
        }
        return $allRule;
    }

    /**
     * 获取角色信息
     * @param $gid
     * @return array|false|null|\PDOStatement|string|Model
     */
    public static function getAuthGroupInfo($gid) {
        $res = db('auth_group')->where('id', $gid)->find();
        return $res;
    }

    /**
     * 更新角色信息
     * @param $gid
     * @array $data
     * @return bool
     */
    public static function updateAuthGroup($gid, $data=[]) {
        $res = db('auth_group')->where('id', $gid)->update($data);
        return $res === false ? false : true;
    }

    /**
     * 添加角色
     * @param array $data
     * @return bool
     */
    public static function addAuthGroup($data=[]) {
        $res = db('auth_group')->insert($data);
        return $res ? true : false;
    }
}