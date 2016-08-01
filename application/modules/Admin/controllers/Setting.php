<?php

/**
 * 文件说明
 *
 * @filename    Setting.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:12
 */
class SettingController extends AdminController {
    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '设置')
        );

        $setting_service              = $this->loadService('Setting');
        $this->output_data['setting'] = $setting_service->getByKeys();
        $this->display('index', $this->output_data);

        return false;
    }

    public function setAction() {
        $post            = $_POST;
        $post            = array_filter($post);
        $setting_service = $this->loadService('Setting');
        $ret             = $setting_service->insertOrUpdate($post);
        if (!$ret['state']) {
            $this->error($ret['message']);
        }
        $this->success([], '设置成功', base_url('admin/setting/index'));
    }
}