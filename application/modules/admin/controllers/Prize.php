<?php

/**
 * 文件说明
 *
 * @filename    Prize.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:11
 */
class PrizeController extends AdminController {
    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '奖品管理')
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

        $prize_service = $this->loadService('Prize');
        $count         = $prize_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $prize_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }

    public function addAction() {
        $p_name        = $this->post('p_name');
        $p_inventory   = $this->post('p_inventory');
        $p_probability = $this->post('p_probability');
        $uid           = $this->userinfo['a_uid'];
        $prize_service = $this->loadService('Prize');
        $ret           = $prize_service->add($uid, $p_name, $p_inventory, $p_probability);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function editAction() {
        $p_id          = $this->post('p_id');
        $p_name        = $this->post('p_name');
        $p_inventory   = $this->post('p_inventory');
        $p_probability = $this->post('p_probability');
        $uid           = $this->userinfo['a_uid'];
        $prize_service = $this->loadService('Prize');
        $ret           = $prize_service->edit($p_id, $uid, $p_name, $p_inventory, $p_probability);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function getPrizeByIdAction() {
        $p_id          = $this->get('p_id');
        $prize_service = $this->loadService('Prize');
        $info          = $prize_service->getItemById($p_id);
        $this->success($info);
    }

    public function delAction() {
        $ids           = $this->get('ids');
        $prize_service = $this->loadService('Prize');
        $ret           = $prize_service->del($ids);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }
}