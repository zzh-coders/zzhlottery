<?php
/**
 * 文件说明
 *
 * @filename    AdminLogModel.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 17:31
 */

namespace Yboard;


class AdminLogModel extends CommonModel{
    public function __construct($options = null) {
        $this->_table = 'lty_admin_log';
        $this->_pk    = 'uid';
        parent::__construct($options);
    }

}