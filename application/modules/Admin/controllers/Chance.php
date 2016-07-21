<?php

/**
 * 文件说明
 *
 * @filename    Chance.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:12
 */
class ChanceController extends AdminController {
    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '抽奖机会管理')
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

        $chance_service = $this->loadService('Chance');
        $count          = $chance_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $chance_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }
}