项目名：zzhlottery


环境：

PHP5.5+


安装：

首先安装yaf

下载地址：https://pecl.php.net/package/yaf


配置信息

[YAF]

extension=yaf.so ;加载so文件

yaf.environ=dev ;环境

yaf.use_namespace=On ;开启命名空间

yaf.use_spl_autoload = 1; 开启sql_autoload,这里的上传累以及日志类会使用到

配置数据库文件：
{rootPath}/conf/application.ini

找到

[dev : common]

;域名配置

base_url = 'http://zzh-lottery.yboard.cn'


;数据库配置

db.type = mysql

db.dbname = zzh-lottery

db.pconnect = 1

db.host = loalhost

db.port = 3306

db.user = root

db.pswd = 123456


配置就好


后端框架：ace

ace集合了bootstap的各种插件，而后我自己加入了bootstap-table(表格插件),bootbox(弹窗插件)


服务器框架 yaf

缓存层 redis（这里从性能上讲最好用redis、memcache，文件的话需要用锁，这有点不合适）

model层用开源框架medoo

前端框架 react.js(学习阶段)

邮箱：

532207815@qq.com

QQ:532207815
