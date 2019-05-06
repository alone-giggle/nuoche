<?php
/**
 * Created by PhpStorm.
 * User: xiucai
 * Date: 2018/3/17
 * Time: 12:32
 */

namespace app\admin\model;

use think\Exception;
use think\Model;
use think\Db;

class AuthUser extends Model
{
    /**
     * 登陆
     * @param $usename
     * @param $password
     * @return bool|null|static
     */
    public static function login($usename, $password) {
        $user_info = self::get(['username'=>$usename]);
        if($user_info['username'] !== $usename) {
            return false;
        }
        if(md5($password . $user_info['salt']) != $user_info['password']) {
            return false;
        }
        return $user_info;
    }

    /**
     * 获取成员列表
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

    /**
     * 统计成员数量
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
     * 管理员添加
     * @param array $data
     * @return bool
     */
    public static function addUser($data=[]) {
        $res = self::insert($data);
        return $res ? true : false;
    }

    /**
     * 更新管理员信息
     * @param $uid
     * @param array $data
     * @return bool
     */
    public static function updateUser($uid, $data=[]) {
        $res = self::where('id', $uid)->update($data);
        return $res === false ? false : true;
    }

    /**
     * 获取管理员信息
     * @param array $where
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getUser($where=[]) {
        $res = self::where($where)->find();
        return $res;
    }

    /**
     * 获取管理员的角色
     * @param $uid
     * @return mixed
     */
    public static function getGroupOfUser($uid) {
        $res = db('auth_group_access')->alias('ga')
            ->field('g.id,g.title')
            ->join('__AUTH_GROUP__ g', 'ga.group_id = g.id', 'left')
            ->where('ga.uid', $uid)
            ->where('g.status', 1)
            ->select();
        return $res;
    }

    /**
     * 更新用户所属角色
     * @param $uid
     * @param array $data
     * @return bool
     */
    public static function updateGroupOfUser($uid, $data=[]) {
        Db::startTrans();
        try{
            $res_1 = Db::name('auth_group_access')->where('uid', $uid)->delete();
            if($data) {
                $newData = [];
                foreach ($data as $d) {
                    $newData[] = ['uid'=>$uid, 'group_id'=>$d];
                }
                $res_2 = Db::name('auth_group_access')->insertAll($newData);
            } else {
                $res_2 = true;
            }
            if($res_1 && $res_2) {
                Db::commit();
                return true;
            } else {
                Db::rollback();
                return false;
            }
        } catch (Exception $exception) {
            Db::rollback();
            return false;
        }
    }
}