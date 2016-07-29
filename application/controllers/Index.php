<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends CommonController {

    private $md5 = '32617dghsafdsya@!#@#$dfs';

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
            $token = $this->getParam('access_token');
            if (!$this->checkToken($token)) {
                $this->error('你还未登录');
            }
            $this->uid = $this->tokenToUid($token);
            if (!$this->uid) {
                $this->error('token非法');
            }
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
     * token常规判断
     * @param $token
     * @return bool
     */
    private function checkToken(&$token) {

        if (!$token) {
            $this->error('参数缺少token');
        }
        if (trim($token) != $token) {
            $this->error('token错误');
        }
        $token = trim($token);

        return true;
    }

    /**
     * 根据token获取登录的uid
     * @param $token
     * @return bool
     * todo::需要用到redis来存储
     */
    private function tokenToUid($token) {
        $user_info = getSession('token_' . $token);
        if (!$user_info || !$user_info['uid']) {
            return false;
        }

        return $user_info['uid'];
    }

    /**
     * 登录操作，
     * 这里需要两个redis来存储（用户信息、token信息）
     */
    public function loginAction() {
        $uid       = 1;
        $user_info = ['uid' => $uid];
        setSession('user_info_' . $uid, $user_info);
        $token = $this->getToken($uid);
        setSession('token_' . $token, $user_info);
        $result_data = ['access_token' => $this->getToken($uid)];
        $this->success($result_data);
    }

    /**
     * 用户uid产生token
     * @param $uid
     * @return string
     */
    private function getToken($uid) {
        return md5($uid . $this->md5);
    }

    /**
     * 初始化抽奖机会
     * 初始化奖品信息
     */
    public function initLotteryAction() {
        $uid                 = $this->uid;
        $date                = CUR_DATE;
        $lottery_service     = $this->loadService('Lottery');
        $is_today_add_chance = $lottery_service->isTodayAddChance($uid, $date);
        if (!$is_today_add_chance) {
            $lottery_service->incChance($uid, $date, $lottery_service->init_chance);
        }

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
