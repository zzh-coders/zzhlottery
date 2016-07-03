<?php
/**
 *
 * @filename    MemberService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     $Id$
 * @since        1.0
 * @time        2016/4/13 0:15
 */

namespace Yboard;

class MemberService extends CommonService {
    public function count($params = null) {
        $params       = $this->parseParams($params);
        $member_model = $this->loadModel('Member');

        return $member_model->countByParams($params);
    }

    public function getList($params, $limit = 0, $page = 20) {
        $params       = $this->parseParams($params);
        $member_model = $this->loadModel('Member');
        $data         = $member_model->getList($params, $limit, $page);

        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }

        return $data;
    }

    public function add($m_phone, $m_username, $m_email, $m_ip) {
        if (!$m_phone) {
            return $this->returnInfo(0, '用户帐号为空');
        }
        if (!$m_username) {
            return $this->returnInfo(0, '用户名为空');
        }
        if (!$m_email) {
            return $this->returnInfo(0, '邮箱为空');
        }
        $member_model = $this->loadModel('Member');
        $member_info  = $member_model->getMemberByUsername($m_username);
        if ($member_info) {
            return $this->returnInfo(0, '用户已经存在');
        }
        $data = array(
            'm_phone'     => $m_phone,
            'm_username'  => $m_username,
            'm_email'     => $m_email,
            'm_ip'        => $m_ip,
            'm_state'     => 1,
            'create_time' => NOW_TIME,
            'm_password'  => md5('lottery.yboard.cn' . '123456'),
            'm_openid'    => ''
        );
        if ($item_id = $member_model->save($data)) {
            return $this->returnInfo(1, '用户添加成功');
        }

        return $this->returnInfo();
    }

    public function edit($m_uid, $m_phone, $m_username, $m_email, $m_ip) {
        if (!$m_uid) {
            return $this->returnInfo(0, '用户id为空');
        }
        if (!$m_phone) {
            return $this->returnInfo(0, '用户帐号为空');
        }
        if (!$m_username) {
            return $this->returnInfo(0, '用户名为空');
        }
        if (!$m_email) {
            return $this->returnInfo(0, '邮箱为空');
        }
        $member_model = $this->loadModel('Member');
        $member_info  = $member_model->getMemberByUsername($m_username);
        if ($member_info && $member_info['m_uid'] != $m_uid) {
            return $this->returnInfo(0, '用户已经存在');
        }
        $data = array(
            'm_phone'    => $m_phone,
            'm_username' => $m_username,
            'm_email'    => $m_email,
            'm_ip'       => $m_ip
        );

        if ($member_model->updateById($m_uid, $data) !== false) {

            return $this->returnInfo(1, '用户编辑成功');
        }

        return $this->returnInfo();
    }


    public function del($ids) {
        if (!$ids) {
            return $this->returnInfo(0, '请选择用户id');
        }
        $member_model = $this->loadModel('Member');

        if ($member_model->deleteByIds($ids) !== false) {
            return $this->returnInfo(1, '用户删除成功');
        }

        return $this->returnInfo();
    }

    public function getMemberById($userId) {
        $member_model = $this->loadModel('Member');

        return $member_model->getById($userId);
    }

}