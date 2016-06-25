<?php

/**
 * 文件说明
 *
 * @filename    FileController.php
 * @author      zouzehua<zzh787272581@163.com>
 * @version     0.1.0
 * @since       0.1.0 11/22/15 oomusou: 新增getLatest3Posts()
 * @time        2016/6/12 22:38
 */
class FileController extends CommonController {

    public function uploadAction() {
        Yaf\Loader::import('Upload.class.php');
        $upload            = new \Yboard\Upload();// 实例化上传类
        $upload->maxSize   = 3145728;// 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  = getConfig('upload_path');// 设置附件上传目录
        $upload->savePath  = '';// 设置附件上传子目录
        $info              = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            $url  = getFileUrl($info['editormd-image-file']['savepath'] . $info['editormd-image-file']['savename']);
            $data = array('success' => 1, 'url' => $url);
            echo json_encode($data, true);
            exit;
        }
    }
}