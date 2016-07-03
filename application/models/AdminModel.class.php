<?php
/**
 * 文件说明
 *
 * @filename    AdminModel.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 17:28
 */

namespace Yboard;


class AdminModel extends CommonModel {
    public function __construct($options = null) {
        $this->_table = 'lty_admin';
        $this->_pk    = 'a_uid';
        parent::__construct($options);
    }

    public function getAdminByUsername($username) {
        if (empty($username)) {
            return null;
        }

        return $this->get($this->_table, '*', ['a_account' => $username]);
    }

}