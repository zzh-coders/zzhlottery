<?php
define('APP_PATH', dirname(__DIR__));
define('APP_DEBUG', true);
$application = new Yaf\Application(APP_PATH . "/conf/application.ini");
define('YAF_ENVIRON', $application->environ());
$application->bootstrap()->run();
?>
