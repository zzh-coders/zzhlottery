<?php

class AdminController extends CommonController {
    public $userinfo;
    public $output_data;
    public $limit;

    public function init() {
        /**
         * 项目是后台管理项目，所以必须要进行的一个操作就是验证是否登录，如果没有登录的话就进行登录操作
         * 登录的控制器是Login/Index地方。
         */
        $this->userinfo = getSession('userinfo');
        if (!$this->userinfo && !$this->noLoginAction()) {
            $this->redirect(base_url('admin/Public/Login'));
        }
        $this->output_data['userinfo'] = $this->userinfo;
        $this->output_data['limit']    = $this->limit = 15;
    }

    public function noLoginAction() {
        $no_login_action = array(
            'Public' => array('login', 'verify', 'register'),
            'Page'   => array('share')
        );
        $request         = $this->getRequest();
        $controller      = $request->controller;
        $action          = $request->action;

        return (key_exists($controller, $no_login_action) && in_array($action, $no_login_action[$controller]));
    }

    public function offset_format($total, $limit, $offset) {
        $total_page = ceil($total / $limit);//总页数
        $pre_page   = intval($offset / $limit);
        if ($pre_page > ($total_page - 1)) {
            $pre_page = $total_page - 1;
        }
        ($pre_page < 0) && $pre_page = 0;

        return $pre_page;
    }

}