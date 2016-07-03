<?php
/**
 * 文件说明
 *
 * @filename    WinningService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/7/3 17:53
 */

namespace Yboard;


class WinningService extends CommonService {
    public function count($params = null) {
        $params             = $this->parseParams($params);
        $winning_info_model = $this->loadModel('WinningInfo');

        return $winning_info_model->countByParams($params);
    }

    public function getList($params, $limit = 0, $page = 20) {
        $params             = $this->parseParams($params);
        $winning_info_model = $this->loadModel('WinningInfo');
        $data               = $winning_info_model->getList($params, $limit, $page);


        if ($data) {
            $pid_array   = array_column($data, 'p_id');
            $prize_model = $this->loadModel('Przie');
            $prize_info  = $prize_model->getByIds($pid_array, '*', 'p_id');

            $uid_array    = array_column($data, 'w_uid');
            $member_model = $this->loadModel('Member');
            $member_info  = $member_model->getByIds($uid_array, '*', 'w_uid');

            foreach ($data as $k => $v) {
                $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
                $data[$k]['username']    = isset($member_info[$v['w_uid']]) ? $member_info[$v['w_uid']]['m_username'] : '';
                $data[$k]['p_name']       = isset($prize_info[$v['p_id']]) ? $member_info[$v['p_id']]['p_name'] : '';
            }
        }


        return $data;
    }
}