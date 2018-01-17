# Host: localhost  (Version: 5.5.53)
# Date: 2018-01-09 16:23:34
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "ecs_admin_user"
#

DROP TABLE IF EXISTS `ecs_admin_user`;
CREATE TABLE `ecs_admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `ec_salt` varchar(10) DEFAULT NULL,
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `nav_list` text NOT NULL,
  `lang_type` varchar(50) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `suppliers_id` smallint(5) unsigned DEFAULT '0',
  `todolist` longtext,
  `role_id` smallint(5) DEFAULT NULL,
  `passport_uid` varchar(20) DEFAULT NULL,
  `yq_create_time` smallint(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "ecs_admin_user"
#

/*!40000 ALTER TABLE `ecs_admin_user` DISABLE KEYS */;
INSERT INTO `ecs_admin_user` VALUES (1,'mindCXL521','1085928815@qq.com','ade88f13e1a6f734fa3a241ff455de74','6390',1514945194,1515457313,'127.0.0.1','all','商品列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit,店铺二维码|lead.php?act=list,服务市场|service_market.php','',0,0,NULL,NULL,NULL,NULL),(2,'88180101856839','88180101856839','shopex',NULL,1514945236,0,'','all','商品列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit,店铺二维码|lead.php?act=list,服务市场|service_market.php','',0,0,NULL,NULL,'88180101856839',32767);
/*!40000 ALTER TABLE `ecs_admin_user` ENABLE KEYS */;
