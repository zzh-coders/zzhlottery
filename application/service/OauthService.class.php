<?php
/**
 * 文件说明
 *
 * @filename    OauthService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/8/2 11:23
 */

namespace Yboard;


class OauthService extends CommonService {
    private $md5 = '32617dghsafdsya@!#@#$dfs';

    /**
     * 用户uid产生token
     * @param $uid
     * @return string
     */
    public function createToken($uid) {
        return md5($uid . $this->md5);
    }

    /**
     * token常规判断
     * @param $token
     * @return bool
     */
    public function checkToken($token) {

        if (!$token) {
            return $this->returnInfo(0, '参数缺少token');
        }
        if (trim($token) != $token) {
            return $this->returnInfo(0, 'token错误');
        }
        $token = trim($token);

        $uid = $this->tokenToUid($token);
        if (!$uid) {
            return $this->returnInfo(0, 'token非法');
        }

        return $this->returnInfo(1, 'token验证成功', ['uid' => $uid]);
    }

    /**
     * 根据token获取登录的uid
     * @param $token
     * @return bool
     */
    private function tokenToUid($token) {
        $redis_class = memory();
        $key         = str_replace('{token}', $token, USER_TOKEN_KEY);
        $user_info   = $redis_class->get($key);
        if (!$user_info || !$user_info['uid']) {
            return false;
        }

        return $user_info['uid'];
    }

}