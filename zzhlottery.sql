DROP DATABASE IF EXISTS zzh_lottery;

CREATE DATABASE `zzh_lottery` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE zzh_lottery;
/*Table structure for table `lty_prize` */

DROP TABLE IF EXISTS `lty_prize`;

CREATE TABLE `lty_prize` (
  `p_id` INT(10) NOT NULL AUTO_INCREMENT COMMENT '奖品id',
  `p_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '奖品名称',
  `p_inventory` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品库存',
  `p_image_uri` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '奖品的图片',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品创建时间',
  `create_uid` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品创建人',
  `p_probability` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品抽中概率，这里概率计算方式为(单个奖品概率/所有奖品概率总和)',
  `p_actual_probability` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品抽中概率,这个为实际概率',
  PRIMARY KEY (`p_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖奖品表';

/*Table structure for table `lty_chance` */

DROP TABLE IF EXISTS `lty_chance`;

CREATE TABLE `lty_chance` (
  `c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '抽奖机会id',
  `c_uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '抽奖人uid',
  `c_num` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '抽奖机会数',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`c_id`),
  INDEX IDX_UID(`c_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖机会';

/*Table structure for table `lty_chance_info` */

DROP TABLE IF EXISTS `lty_chance_info`;

CREATE TABLE `lty_chance_info` (
  `ci_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '抽奖机会明细id',
  `ci_uid` INT(10) NOT NULL DEFAULT '0' COMMENT '抽奖机会明细uid，与抽奖机会中uid是一致的',
  `c_id` INT(1) NOT NULL DEFAULT '0' COMMENT '抽奖id',
  `ci_type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '抽奖机会类型(1=增加，2=减少)',
  `ci_category` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '增加机会的操作方法，这里使用英文字母大写缩写,可以定义相应的枚举数组',
  `ci_num` INT(1) NOT NULL DEFAULT '1' COMMENT '抽奖机会操作数量，均为正数',
  `create_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`ci_id`),
  INDEX IDX_UID(`ci_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='抽奖机会明细';

/*Table structure for table `lty_winning` */

DROP TABLE IF EXISTS `lty_winning`;

CREATE TABLE `lty_winning` (
  `w_id` INT(10) NOT NULL AUTO_INCREMENT COMMENT '中奖id',
  `w_uid` INT(10) NOT NULL DEFAULT '0' COMMENT '中奖用户id',
  `update_time` INT(10) NOT NULL DEFAULT '0' COMMENT '最后中奖时间',
  `update_date` INT(10) NOT NULL DEFAULT '0' COMMENT '最后中奖日期，格式：20160506',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建日期',
  PRIMARY KEY (`w_id`),
  INDEX IDX_UID(`w_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='中奖表';

/*Table structure for table `lty_winning_info` */

DROP TABLE IF EXISTS `lty_winning_info`;

CREATE TABLE `lty_winning_info` (
  `wi_id` INT(10) NOT NULL AUTO_INCREMENT COMMENT '中奖明细id',
  `w_id` INT(10) NOT NULL DEFAULT '0' COMMENT '中奖id',
  `w_uid` INT(10) NOT NULL DEFAULT '0' COMMENT '中奖人',
  `p_id` INT(10) NOT NULL DEFAULT '0' COMMENT '奖品id',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '中奖时间',
  `create_date` INT(10) NOT NULL DEFAULT '0' COMMENT '中奖日期，格式：20160506',
  PRIMARY KEY (`wi_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='中奖日志表';

/*Table structure for table `lty_member` */

DROP TABLE IF EXISTS `lty_member`;

CREATE TABLE `lty_member` (
  `m_uid` INT(10) NOT NULL AUTO_INCREMENT  COMMENT '用户uid',
  `m_phone` INT(15) NOT NULL DEFAULT '0' COMMENT '手机号码',
  `m_password` INT(32) NOT NULL DEFAULT '0' COMMENT '用户密码',
  `m_openid` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '微信的openid',
  `m_username` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `m_email` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户邮箱号码',
  `m_ip` VARCHAR(32) NOT NULL DEFAULT ''  COMMENT '用户注册ip',
  `m_state` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态（1=正常，2=删除）',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`m_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

/*Table structure for table `lty_admin` */

DROP TABLE IF EXISTS `lty_admin`;

CREATE TABLE `lty_admin` (
  `a_uid` INT(10) NOT NULL AUTO_INCREMENT  COMMENT '管理员uid',
  `a_account` VARCHAR(15) NOT NULL DEFAULT '0' COMMENT '管理员登录帐号',
  `a_password` VARCHAR(32) NOT NULL DEFAULT '0' COMMENT '管理员登录密码',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`a_uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='管理员信息表';

/*Table structure for table `lty_admin_log` */

DROP TABLE IF EXISTS `lty_admin_log`;

CREATE TABLE `lty_admin_log` (
  `al_id` INT(10) NOT NULL AUTO_INCREMENT  COMMENT '管理员记录id',
  `al_uid` INT(10) NOT NULL DEFAULT '0'  COMMENT '管理员uid',
  `al_content` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '管理员记录信息，json数据',
  `create_time` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`al_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='管理员操作记录表';
