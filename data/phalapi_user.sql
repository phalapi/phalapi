CREATE TABLE `phalapi_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) DEFAULT '' COMMENT '昵称',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(32) DEFAULT NULL COMMENT '随机加密因子',
  `reg_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `avatar` varchar(500) DEFAULT '' COMMENT '头像',
  `mobile` varchar(20) DEFAULT '' COMMENT '手机号',
  `sex` tinyint(4) DEFAULT '0' COMMENT '性别，1男2女0未知',
  `email` varchar(50) DEFAULT '' COMMENT '邮箱',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_unique_key` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `phalapi_user_session` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT '0' COMMENT '用户id',
  `token` varchar(64) DEFAULT '' COMMENT '登录token',
  `client` varchar(32) DEFAULT '' COMMENT '客户端来源',
  `times` int(6) DEFAULT '0' COMMENT '登录次数',
  `login_time` int(11) DEFAULT '0' COMMENT '登录时间',
  `expires_time` int(11) DEFAULT '0' COMMENT '过期时间',
  `ext_data` text COMMENT 'json data here',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '25', '用户', 'page/phalapi-user/index.html', '5', '1', 'fa fa-users');