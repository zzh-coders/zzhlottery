<?php

/**
 * 文件说明
 *
 * @filename    Winning.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/25 22:13
 */
class WinningController extends AdminController {

    public function indexAction() {
        $this->output_data['nav'] = array(
            array('url' => '', 'name' => '中奖信息')
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

        $winning_service = $this->loadService('Winning');
        $count           = $winning_service->count($params);

        $offset = $this->offset_format($count, $limit, $offset);

        $list = $winning_service->getlist($params, $limit, $offset * $limit);
        $this->ajaxRows($list, $count);
    }
}