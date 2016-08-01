<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, Meizu.com
 * @author   zouzehua <zouzehua@meizu.com>
 * @version $Id: SettingModel.class.php, v ${VERSION} 2016-8-1 11:26 Exp $
 */

namespace Yboard;


class SettingModel extends CommonModel{
    public function __construct($options = null) {
        $this->_table = 'lty_setting';
        $this->_pk    = 'skey';
        parent::__construct($options);
    }
}