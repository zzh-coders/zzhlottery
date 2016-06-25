<?php
/**
 * 文件说明
 *
 * @filename    AdminService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 17:29
 */

namespace Yboard;


class AdminService extends CommonService {
    public function register($username, $password, $confirm_pass, $code) {
        \Yaf\Loader::import('Verify.class.php');
        $verify = new \Yboard\Verify();
        if (!$verify->check($code, 1)) {
            return $this->returnInfo(0, '验证码输入错误');
        }
        if ($confirm_pass != $password) {
            return $this->returnInfo(0, '确认密码不正确');
        }
        if (empty($username)) {
            return $this->returnInfo(0, '用户不能为空');
        }
        if (empty($password)) {
            return $this->returnInfo(0, '密码不能为空');
        }
        $admin_model = $this->loadModel('Admin');

        $admin_info = $admin_model->fetch_by_username($username);
        if (!$admin_info) {
            $admin_info        = [
                'a_account'    => $username,
                'a_password'    => md5(substr($username, -5) . $password),
                'create_time' => NOW_TIME
            ];
            $uid           = $admin_model->save($admin_info);
            if(!$uid){
                return $this->returnInfo(0, '用户注册失败');
            }
            $admin_info['a_uid'] = $uid;
        } else {
            return $this->returnInfo(0, '用户已经存在');
        }
        setSession('userinfo', $admin_info);
        $admin_log_model = $this->loadModel('AdminLog');
        $admin_log_model->save([
            'al_uid'=>$admin_info['a_uid'],
            'al_content'=>'用户注册',
            'create_time'=>NOW_TIME,
        ]);
        return $this->returnInfo(1, '注册成功');
    }

    public function login($username, $password) {
        if (empty($username)) {
            return $this->returnInfo(0, '用户不能为空');
        }
        if (empty($password)) {
            return $this->returnInfo(0, '密码不能为空');
        }
        $admin_model = $this->loadModel('Admin');

        $admin_info = $admin_model->fetch_by_username($username);
        if (!$admin_info) {
            return $this->returnInfo(0, '用户不存在');
        }
        if ($admin_info['a_password'] != md5(substr($username, -5) . $password)) {
            return $this->returnInfo(0, '用户密码不正确');
        }
        setSession('userinfo', $admin_info);

        $admin_log_model = $this->loadModel('AdminLog');
        $admin_log_model->save([
            'al_uid'=>$admin_info['a_uid'],
            'al_content'=>'用户登录',
            'create_time'=>NOW_TIME,
        ]);
        return $this->returnInfo(1, '登录成功');
    }
    
}