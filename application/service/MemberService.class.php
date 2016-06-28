<?php
/**
 *
 * @filename    MemberService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     $Id$
 * @since        1.0
 * @time        2016/4/13 0:15
 */

namespace Yboard;

class MemberService extends CommonService {
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
        $member_model = $this->loadModel('member');

        $member = $member_model->fetch_by_username($username);
        if (!$member) {
            $member        = [
                'username'    => $username,
                'password'    => md5(substr($username, -5) . $password),
                'create_time' => NOW_TIME
            ];
            $uid           = $member_model->save($member);
            $member['uid'] = $uid;
        } else {
            return $this->returnInfo(0, '用户已经存在');
        }

        setSession('userinfo', $member);

        return $this->returnInfo(1, '注册成功');
    }

    public function login($username, $password) {
        if (empty($username)) {
            return $this->returnInfo(0, '用户不能为空');
        }
        if (empty($password)) {
            return $this->returnInfo(0, '密码不能为空');
        }
        $member_model = $this->loadModel('member');

        $member = $member_model->fetch_by_username($username);
        if (!$member) {
            return $this->returnInfo(0, '用户不存在');
        }
        if (!$member['state']) {
            return $this->returnInfo(0, '用户禁用');
        }
        if ($member['password'] != md5(substr($username, -5) . $password)) {
            return $this->returnInfo(0, '用户密码不正确');
        }
        setSession('userinfo', $member);

        return $this->returnInfo(1, '登录成功');
    }

    public function getInfoById($userId) {
        if (!$userId) {
            return [];
        }
        $member_model = $this->loadModel('Member');

        return $member_model->getById($userId);
    }
}