<?php

class CommonController extends Yaf\Controller_Abstract {

    public function post($key, $filter = true) {
        if ($filter) {
            return filterStr($this->getRequest()->getPost($key));
        } else {
            return $this->getRequest()->getPost($key);
        }
    }

    public function getParam($key, $filter = true) {
        if ($this->getRequest()->isGet()) {
            if ($filter) {
                return filterStr($this->getRequest()->get($key));
            } else {
                return $this->getRequest()->get($key);
            }
        } else {
            if ($filter) {
                return filterStr($this->getRequest()->getPost($key));
            } else {
                return $this->getRequest()->getPost($key);
            }
        }
    }


    public function getQuery($key, $filter = true) {
        if ($filter) {
            return filterStr($this->getRequest()->getQuery($key));
        } else {
            return $this->getRequest()->getQuery($key);
        }
    }


    public function get($key, $filter = true) {
        if ($filter) {
            return filterStr($this->getRequest()->get($key));
        } else {
            return $this->getRequest()->get($key);
        }
    }

    public function loadService($service_name) {
        Yaf\Loader::import('CommonService.class.php');
        $service_name = ucfirst($service_name);
        static $services;
        if (isset($services[$service_name]) && $services[$service_name]) {
            return $services[$service_name];
        }
        $file = SERVICE_PATH . '/' . $service_name . 'Service.class.php';
        if (PHP_OS == 'Linux') {
            Yaf\Loader::import($file);
        } else {
            require_once $file;
        }
        $class                   = "\\Yboard\\" . $service_name . 'Service';
        $service                 = new $class();
        $services[$service_name] = $service;

        return $service;
    }


    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed  $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function error($message = '', $jumpUrl = '') {
        $data = array('code' => 0, 'data' => [], 'message' => $message, 'url' => $jumpUrl);
        echo json_encode($data, true);
        exit;
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed  $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function success($data = [], $message = '', $jumpUrl = '') {
        $data = array('code' => 200, 'data' => $data, 'message' => $message, 'url' => $jumpUrl);
        echo json_encode($data, true);
        exit;
    }

    /**
     * 这是返回table数据的方法，用的bsTable来做的，特定的规则。
     * @param $rows     数组
     * @param $total    总条数
     */
    protected function ajaxRows($rows, $total) {
        echo json_encode(array('total' => $total, 'rows' => $rows));
        exit;
    }

}