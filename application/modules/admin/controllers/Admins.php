<?php

/**
 * 文件说明
 *
 * @filename    Admin.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:11
 */
class AdminsController extends AdminController {
    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '管理员管理')
        );
        $this->display('index', $this->output_data);

        return false;
    }

    public function getListAction() {
        $offset = $this->get('offset');
        $limit  = $this->get('limit');
        $params = $this->get('search');
        if ($params) {
            $params = json_decode($params, true);
        }

        $admin_service = $this->loadService('Admin');
        $count         = $admin_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $admin_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }

    public function addAction() {
        $a_account     = $this->post('a_account');
        $a_password    = $this->post('a_password');
        $admin_service = $this->loadService('Admin');
        $ret           = $admin_service->add($a_account, $a_password);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function editAction() {
        $a_account     = $this->post('a_account');
        $a_password    = $this->post('a_password');
        $a_uid         = $this->post('a_uid');
        $admin_service = $this->loadService('Admin');
        $ret           = $admin_service->edit($a_uid, $a_account, $a_password);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function getAdminByIdAction() {
        $a_uid         = $this->get('a_uid');
        $admin_service = $this->loadService('Admin');
        $info          = $admin_service->getAdminById($a_uid);
        $this->success($info);
    }

    public function delAction() {
        $ids           = $this->get('ids');
        $admin_service = $this->loadService('Admin');
        $ret           = $admin_service->del($ids);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function adminLogAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '管理员日志')
        );
        $this->display('adminlog', $this->output_data);

        return false;
    }

    public function getAdminLogListAction() {
        $offset = $this->get('offset');
        $limit  = $this->get('limit');
        $params = $this->get('search');
        if ($params) {
            $params = json_decode($params, true);
        }

        $admin_service = $this->loadService('Admin');
        $count         = $admin_service->countAdminLog($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $admin_service->getAdminLoglist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }
}