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

use Yaf\Exception;

class CommonService {
    public function upload() {
        loadFile('Upload.class.php');
        $upload            = new \Yboard\Upload();// 实例化上传类
        $upload->maxSize   = 3145728;// 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  = getConfig('upload_path');// 设置附件上传目录
        $upload->savePath  = '';// 设置附件上传子目录
        $info              = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            return $this->returnInfo(0, $upload->getError());
        } else {// 上传成功 获取上传文件信息
            return $this->returnInfo(1, '成功', $info);
//            $url  = getFileUrl($info[$file_name]['savepath'] . $info[$file_name]['savename']);
//            $data = array('success' => 1, 'url' => $url);
//            echo json_encode($data, true);
//            exit;
        }
    }

    protected function returnInfo($state = 0, $message = '系统错误', $extra = []) {
        return ['state' => $state, 'message' => $message, 'extra' => $extra];
    }

    protected function loadModel($table) {
        try {
            loadFile(['Model.class.php', 'CommonModel.class.php']);
            $table = ucfirst($table);
            static $models;
            if (isset($models[$table]) && $models[$table]) {
                return $models[$table];
            }
            $file = MODEL_PATH . '/' . $table . 'Model.class.php';
            loadFile($file);
            $class          = "\\Yboard\\" . $table . 'Model';
            $model          = new $class();
            $models[$table] = $model;

            return $model;
        } catch (Exception $e) {
            E($e->getMessage());
        }

    }

    protected function parseParams($params) {
        if ($params) {
            $params = array_filter($params, function ($value) {
                if ($value === '' || $value === false || is_null($value)) {
                    return false;
                }

                return true;
            });
        }

        return $params ? $params : [];
    }
}