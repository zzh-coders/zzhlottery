<?php
/**
 * 文件说明
 *
 * @filename    PrizeModel.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/28 22:45
 */

namespace Yboard;


class PrizeModel extends CommonModel {
    public function __construct($options = null) {
        $this->_table = 'lty_prize';
        $this->_pk    = 'p_id';
        parent::__construct($options);
    }

    public function getInfoByPrizeName($p_name) {
        $result_data = [];
        if (!$p_name) {
            return $result_data;
        }

        $result_data = $this->get($this->_table, '*', ['p_name' => $p_name]);

        return $result_data;
    }

    public function getList($params, $page, $limit) {
        $params = $params ? $params : [];
        $where  = array_merge(
            $params,
            [
                'ORDER' => 'create_time DESC',
                'LIMIT' => [(int)$limit, (int)$page]
            ]
        );
        $result = $this->select($this->_table, '*', $where);

        return $result;
    }

    /**
     * 叠加库存
     * @param $p_id
     * @param $p_inventory
     * @return bool|int
     */
    public function increaseInventory($p_id, $p_inventory) {
        if (!$p_inventory) {
            return 1;
        }
        $update_data = [];
        if ((int)$p_inventory > 0) {
            $update_data['p_inventory[+]'] = abs($p_inventory);
        } else {
            $update_data['p_inventory[+]'] = abs($p_inventory);
        }

        return $this->update($this->_table, $update_data, ['p_id' => $p_id]);
    }

}