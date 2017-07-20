
DROP TABLE IF EXISTS `tbl_curd`;
CREATE TABLE `tbl_curd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `content` text,
  `state` tinyint(4) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_curd` VALUES ('1', 'PhalApi', '欢迎使用PhalApi 2.x 版本!', '0', '2017-07-08 12:09:43');
INSERT INTO `tbl_curd` VALUES ('2', '版本更新', '主要改用composer和命名空间，并遵循psr-4规范。', '1', '2017-07-08 12:10:58');
