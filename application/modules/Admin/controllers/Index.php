<?php

/**
 * 文件说明
 *
 * @filename    Index.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 17:50
 */
class IndexController extends AdminController {

    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '首页管理')
        );
        $this->display('index', $this->output_data);

        return false;
    }
}