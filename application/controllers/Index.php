<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends CommonController {

    public function indexAction() {
        echo 111;exit;
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '首页管理')
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
        $params['uid'] = $this->userinfo['uid'];
        $item_service = $this->loadService('Item');
        $count        = $item_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $item_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }

    public function addAction() {
        $item_name        = $this->post('item_name');
        $item_description = $this->post('item_description');
        $uid              = $this->userinfo['uid'];
        $item_service     = $this->loadService('Item');
        $ret              = $item_service->add($uid, $item_name, $item_description);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function editAction() {
        $item_id          = $this->post('item_id');
        $item_name        = $this->post('item_name');
        $item_description = $this->post('item_description');
        $uid              = $this->userinfo['uid'];
        $item_service     = $this->loadService('Item');
        $ret              = $item_service->edit($item_id, $uid, $item_name, $item_description);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }

    public function getItemByIdAction() {
        $item_id      = $this->get('item_id');
        $item_service = $this->loadService('Item');
        $info         = $item_service->getItemById($item_id);
        $this->success($info);
    }

    public function delAction() {
        $ids          = $this->get('ids');
        $item_service = $this->loadService('Item');
        $ret          = $item_service->del($ids);
        if ($ret['state']) {
            $this->success([], $ret['message']);
        }
        $this->error($ret['message']);
    }
}
