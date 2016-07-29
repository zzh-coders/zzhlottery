<?php
/**
 * 文件说明
 *
 * @filename    WinningModel.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/7/3 18:00
 */

namespace Yboard;


class WinningModel extends CommonModel {
    public function __construct($options = null) {
        $this->_table = 'lty_winning';
        $this->_pk    = 'w_id';
        parent::__construct($options);
    }

    public function getByUidAndDate($uid, $date) {
        if (!$uid || !$date) {
            return [];
        }

        return $this->get($this->_table, '*', ['and' => ['w_uid' => $uid, 'update_date' => $date]]);
    }
}