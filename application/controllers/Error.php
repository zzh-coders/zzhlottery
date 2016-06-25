<?php

/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf\Controller_Abstract {

    //从2.1开始, errorAction支持直接通过参数获取异常
    public function errorAction(\Yaf\Exception $exception) {
        if (APP_DEBUG) {
            echo $exception->getMessage();
        } else {
            Yboard\Log::record($exception->getMessage(), \Yboard\Log::ERR);
        }
        
        return false;
    }
}
