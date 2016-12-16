<?php
define('REDIS_PRE', 'lty:');
//奖品的库存key��
define('INVENTORY_KEY', REDIS_PRE . 'inventory:{p_id}');

//用户的抽奖机会key
define('USER_CHANCE_KEY', REDIS_PRE . 'chance:{uid}:{today}');

//今天是否初始化抽奖机会key��
define('USER_ISCHANCE_KEY', REDIS_PRE . 'ischance:{uid}:{today}');

//用户信息key
define('USERINFO_KEY', REDIS_PRE . 'userinfo:{uid}');

//今天是否中奖key��
define('USER_ISWINNING_KEY', REDIS_PRE . 'iswinning:{uid}:{today}');

//token对应的uid信息
define('USER_TOKEN_KEY', REDIS_PRE . 'token:{token}');

?>