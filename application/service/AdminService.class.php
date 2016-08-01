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

        $admin_info = $admin_model->getAdminByUsername($username);
        if (!$admin_info) {
            $admin_info = [
                'a_account'   => $username,
                'a_password'  => md5(substr($username, -5) . $password),
                'create_time' => NOW_TIME
            ];
            $uid        = $admin_model->save($admin_info);
            if (!$uid) {
                return $this->returnInfo(0, '用户注册失败');
            }
            $admin_info['a_uid'] = $uid;
        } else {
            return $this->returnInfo(0, '用户已经存在');
        }
        setSession('userinfo', $admin_info);
        $admin_log_model = $this->loadModel('AdminLog');
        $admin_log_model->save([
            'al_uid'      => $admin_info['a_uid'],
            'al_content'  => '用户注册',
            'create_time' => NOW_TIME,
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

        $admin_info = $admin_model->getAdminByUsername($username);
        if (!$admin_info) {
            return $this->returnInfo(0, '用户不存在');
        }
        if ($admin_info['a_password'] != md5(substr($username, -5) . $password)) {
            return $this->returnInfo(0, '用户密码不正确');
        }
        setSession('userinfo', $admin_info);

        $admin_log_model = $this->loadModel('AdminLog');
        $admin_log_model->save([
            'al_uid'      => $admin_info['a_uid'],
            'al_content'  => '用户登录',
            'create_time' => NOW_TIME,
        ]);

        return $this->returnInfo(1, '登录成功');
    }

    public function count($params = null) {
        $params      = $this->parseParams($params);
        $admin_model = $this->loadModel('Admin');

        return $admin_model->countByParams($params);
    }

    public function getList($params, $limit = 0, $page = 20) {
        $params      = $this->parseParams($params);
        $admin_model = $this->loadModel('Admin');
        $data        = $admin_model->getList($params, $limit, $page);

        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }

        return $data;
    }

    public function add($a_account, $a_password) {
        if (!$a_account) {
            return $this->returnInfo(0, '用户名为空');
        }
        if (!$a_password) {
            $a_password = 123456;
        }
        $admin_model = $this->loadModel('Admin');
        $admin_info  = $admin_model->getAdminByUsername($a_account);
        if ($admin_info) {
            return $this->returnInfo(0, '用户已经存在');
        }
        $data = array(
            'a_account'   => $a_account,
            'a_password'  => md5(substr($a_account, -5) . $a_password),
            'create_time' => NOW_TIME,
        );
        if ($item_id = $admin_model->save($data)) {
            return $this->returnInfo(1, '用户添加成功');
        }

        return $this->returnInfo();
    }

    public function edit($a_uid, $a_account, $a_password) {
        if (!$a_uid) {
            return $this->returnInfo(0, '用户id为空');
        }
        if (!$a_account) {
            return $this->returnInfo(0, '用户帐号为空');
        }
        $admin_model = $this->loadModel('Admin');
        $admin_info  = $admin_model->getAdminByUsername($a_account);
        if ($admin_info && $admin_info['a_uid'] != $a_uid) {
            return $this->returnInfo(0, '用户已经存在');
        }
        $data = array(
            'a_account' => $a_account,
        );
        if ($a_password) {
            $data['a_password'] = md5(substr($a_account, -5) . $a_password);
        }

        if ($admin_model->updateById($a_uid, $data) !== false) {

            return $this->returnInfo(1, '用户编辑成功');
        }

        return $this->returnInfo();
    }


    public function del($a_ids) {
        if (!$a_ids) {
            return $this->returnInfo(0, '请选择管理用户id');
        }
        $admin_model = $this->loadModel('Admin');

        if ($admin_model->deleteByIds($a_ids) !== false) {
            return $this->returnInfo(1, '管理用户删除成功');
        }

        return $this->returnInfo();
    }

    public function getAdminById($p_id) {
        $admin_model = $this->loadModel('Admin');

        return $admin_model->getById($p_id);
    }

    public function countAdminLog($params = null) {
        $params         = $this->parseParams($params);
        $adminlog_model = $this->loadModel('AdminLog');

        return $adminlog_model->countByParams($params);
    }

    public function getAdminLogList($params, $limit = 0, $page = 20) {
        $params         = $this->parseParams($params);
        $adminlog_model = $this->loadModel('AdminLog');
        $data           = $adminlog_model->getList($params, $limit, $page);
        $uid_array      = array_column($data, 'al_uid');
        $admin_model    = $this->loadModel('Admin');
        $admin_member   = $admin_model->getByIds($uid_array, '*', 'a_uid');
        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $data[$k]['username']    = isset($admin_member[$v['al_uid']]) ? $admin_member[$v['al_uid']]['a_account'] : '';
        }

        return $data;
    }
}