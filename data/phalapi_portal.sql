
CREATE TABLE `phalapi_portal_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员账号',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(64) NOT NULL DEFAULT '' COMMENT '盐值',
  `role` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '管理员角色，admin普通管理员，super超级管理员',
  `state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态，1可用0禁止',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `phalapi_portal_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  `target` varchar(10) DEFAULT '_self',
  `sort_num` int(11) DEFAULT '0',
  `parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

