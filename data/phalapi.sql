
DROP TABLE IF EXISTS `phalapi_curd`;
CREATE TABLE `phalapi_curd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `content` text,
  `state` tinyint(4) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `phalapi_curd` VALUES ('1', 'PhalApi', '欢迎使用PhalApi 2.x 版本!', '0', '2017-07-08 12:09:43');
INSERT INTO `phalapi_curd` VALUES ('2', '版本更新', '主要改用composer和命名空间，并遵循psr-4规范。', '1', '2017-07-08 12:10:58');


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
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4;

insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '1', '运营后台', null, '1', '0', 'fa fa-address-book');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2', '页面示例', null, '2', '0', 'fa fa-address-book');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '3', '应用市场', 'http://www.phalapi.net', '3', '0', 'fa fa-rocket');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '21', '应用市场', 'page/phalapi-plugins/index.html', '1', '3', 'fa fa-rocket');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '22', '我的应用', 'page/phalapi-plugins/mine.html', '2', '3', 'fa fa-rocket');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '23', '菜单管理', 'page/menu.html', '2', '1', 'fa fa-window-maximize');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '24', 'CURD表格示例', 'page/phalapi-curd-table/index.html', '5', '1', 'fa fa-list-alt');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '101', '首页', 'page/welcome-1.html', '1', '1', 'fa fa-home');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '201', '系统设置', 'page/setting.html', '0', '2', 'fa fa-gears');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '202', '表格示例', 'page/table.html', '0', '2', 'fa fa-file-text');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '203', '分步表单', 'page/form-step.html', '0', '2', 'fa fa-navicon');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '204', '其它界面', null, '0', '2', 'fa fa-snowflake-o');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '205', '组件', null, '0', '2', 'fa fa-lemon-o');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2001', '表单示例', null, '0', '202', 'fa fa-calendar');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2002', '普通表单', 'page/form.html', '0', '202', 'fa fa-list-alt');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2003', '按钮示例', 'page/button.html', '0', '204', 'fa fa-snowflake-o');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2004', '弹出层', 'page/layer.html', '0', '204', 'fa fa-shield');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2005', '图标列表', 'page/icon.html', '0', '205', 'fa fa-dot-circle-o');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2006', '文件上传', 'page/upload.html', '0', '205', 'fa fa-arrow-up');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2007', '富文本编辑器', 'page/editor.html', '0', '205', 'fa fa-edit');
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '2008', '省市县区选择器', 'page/area.html', '0', '205', 'fa fa-rocket');


ALTER  TABLE `phalapi_portal_menu` ADD `assign_admin_roles` varchar(1000) DEFAULT '' COMMENT '管理员角色分配，多个用竖线分割';
ALTER  TABLE `phalapi_portal_menu` ADD `assgin_admin_usernames` text COMMENT '分配的管理员ID，多个用竖线分割';

CREATE TABLE `phalapi_portal_admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员角色',
  `role_name` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员角色名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `phalapi_portal_admin_role` VALUES ('1', 'super', '超级管理员'), ('2', 'admin', '普通管理员');

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

