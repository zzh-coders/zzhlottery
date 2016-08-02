<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends CommonController {

    private $uid;


    /**
     * 全局初始化
     */
    public function init() {
        /**
         * 判断用户登录情况
         */
        if (!$this->noLoginAction()) {
            /**
             * 传递过来了access_token，默认为已经登录用户
             * 1、如果token传递出问题，那么判断没有登录
             * 2、token传递过来没有找到用户uid，那么提示token非法
             */
            $token         = $this->getParam('access_token');
            $oauth_service = $this->loadService('Oauth');
            $ret           = $oauth_service->checkToken($token);
            if (!$ret['state']) {
                $this->error($ret['message']);
            }
            $this->uid       = $ret['extra']['uid'];
            $setting_service = $this->loadService('Setting');
            $setting         = $setting_service->getBykeys();
            \Yaf\Registry::set('setting', $setting);
            unset($setting);
        }

    }

    /**
     * 判断当前请求接口是否需要登录操作
     * @return bool
     */
    private function noLoginAction() {
        $no_login_action = array(
            'Index' => array('login'),
        );
        $request         = $this->getRequest();
        $controller      = $request->controller;
        $action          = $request->action;

        return (key_exists($controller, $no_login_action) && in_array($action, $no_login_action[$controller]));
    }


    /**
     * 登录操作，
     * 这里需要两个redis来存储（用户信息、token信息）
     */
    public function loginAction() {
        $uid         = 1;
        $user_info   = ['uid' => $uid];
        $user_key    = str_replace('{uid}', $uid, USERINFO_KEY);
        $redis_class = memory();
        $redis_class->setex($user_key, $user_info);
        $oauth_service = $this->loadService('Oauth');
        $token         = $oauth_service->createToken($uid);
        $key           = str_replace('{token}', $token, USER_TOKEN_KEY);

        $redis_class->setex($key, ['uid' => $uid, 'expire' => (NOW_TIME + 2592000)], 2592000);

        $result_data = ['access_token' => $token];
        $this->success($result_data);
    }


    /**
     * 初始化抽奖机会
     * 初始化奖品信息
     */
    public function initLotteryAction() {
        $uid             = $this->uid;
        $date            = CUR_DATE;
        $lottery_service = $this->loadService('Lottery');

        $lottery_service->initChance($uid, $date);
        $prize_array = $lottery_service->getPrize();
        $this->success($prize_array);
    }

    /**
     *抽奖操作
     * 返回是奖品id，剩余抽奖机会
     */
    public function lotteryAction() {
        $uid             = $this->uid;
        $date            = CUR_DATE;
        $lottery_service = $this->loadService('Lottery');
        $ret             = $lottery_service->lottery($uid, $date);
        if (!$ret['state']) {
            $this->error($ret['message']);
        }
        $this->success($ret['extra']);
    }
}
