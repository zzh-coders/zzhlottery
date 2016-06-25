<?php

/**
 *
 * @filename    CommonService.class.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     $Id$
 * @since        1.0
 * @time        2016/4/13 0:07
 */
namespace Yboard;

class CommonService {
    protected function returnInfo($state = 0, $message = '系统错误', $extra = []) {
        return ['state' => $state, 'message' => $message, 'extra' => $extra];
    }

    protected function loadModel($table) {
        \Yaf\Loader::import('Model.class.php');
        \Yaf\Loader::import('CommonModel.class.php');
        $table = ucfirst($table);
        static $models;
        if (isset($models[$table]) && $models[$table]) {
            return $models[$table];
        }
        $file = MODEL_PATH . '/' . $table . 'Model.class.php';
        if (PHP_OS == 'Linux') {
            \Yaf\Loader::import($file);
        } else {
            require_once $file;
        }
        $class          = "\\Yboard\\" . $table . 'Model';
        $model          = new $class();
        $models[$table] = $model;

        return $model;
    }

    protected function parseParams($params) {
//        if (is_array($params) && !empty($params)) {
//            foreach ($params as $k => $v) {
//                if (empty($params[$k])) {
//                    unset($params[$k]);
//                }
//            }
//        }
        if ($params) {
            $params = array_filter($params, function ($value) {
                if ($value === '' || $value === false || is_null($value)) {
                    return false;
                }

                return true;
            });
        }

        return $params;
    }
}