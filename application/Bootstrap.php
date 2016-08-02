<?php

/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract {

    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $arrConfig);
        Yaf\Loader::import(APP_PATH . "/conf/constants.php");
        Yaf\Loader::import(APP_PATH . "/conf/redis_key.php");
    }

    public function _initCore() {
        date_default_timezone_set('Asia/Chongqing');
        define('CUR_DATE', date('Y-m-d'));
        define('CUR_DATETIME', date('Y-m-d H:I:s'));
        define('NOW_TIME', time());
        define('APP_NAME', 'LIGTHERP');
        define('LIB_PATH', APP_PATH . '/application/library/');
        define('MODEL_PATH', APP_PATH . '/application/models');
        define('SERVICE_PATH', APP_PATH . '/application/service');
        define('FUNC_PATH', APP_PATH . '/application/function');
        define('MODULES_PATH', APP_PATH . '/application/modules');

        // CSS, JS, IMG PATH
        define('__CSS__', '/css/');
        define('__JS__', '/js/');
        define('__IMG__', '/image/');

        // Admin CSS, JS PATH
        define('__ASSETS__', '/assets/');
        define('__HEADER__', APP_PATH . '/public/header.phtml');
        define('__FOOTER__', APP_PATH . '/public/footer.phtml');
    }


    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册一个插件
        $objRouterPlugin = new RouterPlugin();
        $dispatcher->registerPlugin($objRouterPlugin);
    }

    public function _initRoute(Yaf\Dispatcher $dispatcher) {
//        $router = Yaf\Dispatcher::getInstance()->getRouter();
//        //单个信息查询路由
//        $route = new Yaf\Route\Rewrite('pages/:page_id/:item_id', ['module' => 'Index', 'controller' => 'Page', 'action' => 'show']);
//        //使用路由器装载路由协议
//        $router->addRoute('page', $route);
//
//        //单个项目查询路由
//        $route = new Yaf\Route\Rewrite('items/item_id', ['module' => 'Index', 'controller' => 'Item', 'action' => 'show']);
//        $router->addRoute('item', $route);
//
//        //分享地址
//        $route = new Yaf\Route\Rewrite('shares/:page_id/:item_id', ['module' => 'Index', 'controller' => 'Page', 'action' => 'share']);
//        //使用路由器装载路由协议
//        $router->addRoute('share', $route);

    }

    public function _initView(Yaf\Dispatcher $dispatcher) {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }

    public function _initAutoloader(Yaf\Dispatcher $dispatcher) {
        Yaf\Loader::import(FUNC_PATH . '/functions.php');
        loadFile([
            'CommonController.class.php',
            MODULES_PATH . '/Admin/controllers/Admin.php',
            MODULES_PATH . '/Api/controllers/Api.php'
        ]);
    }
}
