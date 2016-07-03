<?php
/**
 * 文件说明
 *
 * @filename    ChanceService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/7/3 18:00
 */

namespace Yboard;


class ChanceService extends CommonService {
    public function count($params = null) {
        $params        = $this->parseParams($params);
        $chanche_model = $this->loadModel('Chance');

        return $chanche_model->countByParams($params);
    }

    public function getList($params, $limit = 0, $page = 20) {
        $params       = $this->parseParams($params);
        $chanche_model = $this->loadModel('Chance');
        $data         = $chanche_model->getList($params, $limit, $page);

        $uid_array    = array_column($data, 'c_uid');
        $member_model = $this->loadModel('Member');
        $member_info  = $member_model->getByIds($uid_array, '*', 'm_uid');

        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $data[$k]['username']    = isset($member_info[$v['c_uid']]) ? $member_info[$v['c_uid']]['m_username'] : '';
        }

        return $data;
    }

}