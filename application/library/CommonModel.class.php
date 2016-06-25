<?php
namespace Yboard;

/**
 * 文件说明
 *
 * @filename    CommonModel.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/5/31 22:36
 */
class CommonModel extends Model {

    public $_table;
    public $_pk;

    public function __construct($options = null) {
        $config  = getConfig('db');
        $options = [
            'database_type' => $config['type'],
            'database_name' => $config['dbname'],
            'server'        => $config['host'],
            'username'      => $config['user'],
            'password'      => $config['pswd'],
            'charset'       => 'utf-8',
            // 可选参数
            'port'          => $config['port'],
            'option'        => [
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ];
        parent::__construct($options);
    }

    public function getById($id, $column = '*') {
        if (!$id) {
            return [];
        }

        return $this->get($this->_table, $column, [$this->_pk => $id]);
    }

    public function countByParams($params, $column = '*') {

        return $this->count($this->_table, $column, $params);
    }

    public function getList($params, $page, $limit) {
        return $this->select($this->_table, '*', array_merge($params, ['LIMIT' => [$limit, $page]]));

    }

    public function save($datas) {
        return parent::insert($this->_table, $datas);
    }

    public function deleteById($id) {
        $where = [$this->_pk => $id];

        return $this->delete($this->_table, $where);
    }

    public function deleteByIds($ids) {
        $where = [$this->_pk => $ids];

        return $this->delete($this->_table, $where);
    }

    public function updateById($id, $data) {
        if (!$id) {
            return false;
        }

        return $this->update($this->_table, $data, [$this->_pk => $id]);
    }
}