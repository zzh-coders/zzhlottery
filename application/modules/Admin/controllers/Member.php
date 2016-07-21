<?php

/**
 * 文件说明
 *
 * @filename    Member.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:11
 */
class MemberController extends AdminController {
    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '用户管理')
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

        $member_service = $this->loadService('Member');
        $count          = $member_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $member_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }

    public function addAction() {
        $m_phone        = $this->post('m_phone');
        $m_username     = $this->post('m_username');
        $m_email        = $this->post('m_email');
        $m_ip           = getClientIp();
        $member_service = $this->loadService('Member');
        $ret            = $member_service->add($m_phone, $m_username, $m_email, $m_ip);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function editAction() {
        $m_phone        = $this->post('m_phone');
        $m_username     = $this->post('m_username');
        $m_email        = $this->post('m_email');
        $m_ip           = getClientIp();
        $m_uid          = $this->post('m_uid');
        $member_service = $this->loadService('Member');
        $ret            = $member_service->edit($m_uid, $m_phone, $m_username, $m_email, $m_ip);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function getMemberByIdAction() {
        $m_uid          = $this->get('m_uid');
        $member_service = $this->loadService('Member');
        $info           = $member_service->getMemberById($m_uid);
        $this->success($info);
    }

    public function delAction() {
        $ids            = $this->get('ids');
        $member_service = $this->loadService('Member');
        $ret            = $member_service->del($ids);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }
}