<?php
class PublicController extends AdminController {
    public function loginAction() {
        if (IS_AJAX) {
            $username = $this->post('username');
            $password = $this->post('password', 'post');
//            $verify         = getRequest('verify', 'post');
            $admin_service = $this->loadService('Admin');

//            $ret = $member_service->verifyLogin($username, $password, $verify);
            $ret = $admin_service->login($username, $password);
            if ($ret['state']) {
                $this->success([], '登录成功', base_url('admin/Index/index'));
            }
            $this->error($ret['message']);
        }
    }

    public function registerAction() {
        if (IS_AJAX) {
            $username       = $this->post('username');
            $password       = $this->post('password');
            $confirm_pass   = $this->post('confirm_pass');
            $verify         = $this->post('verify');
            $admin_service = $this->loadService('admin');

            $ret = $admin_service->register($username, $password, $confirm_pass, $verify);
            if ($ret['state']) {
                $this->success([], '注册成功', base_url('admin/Index/index'));
            }
            $this->error($ret['message']);
        }
    }

    public function verifyAction() {
        ob_clean();
        Yaf\Loader::import(LIB_PATH . '/Verify.class.php');
        $verify = new \Yboard\Verify([
            'imageW'=>290
        ]);
        $verify->entry(1);

        return false;
    }

    public function loginoutAction() {
        clearSession('userinfo');
        $this->success([], '退出成功', base_url('admin/Public/login'));

        return false;
    }
}