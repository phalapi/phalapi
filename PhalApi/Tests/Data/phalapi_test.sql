
-- ----------------------------
-- Table structure for `tbl_demo`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_demo`;
CREATE TABLE `tbl_demo` (
  `id` int(11) NOT NULL  AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_demo
-- ----------------------------
INSERT INTO `tbl_demo` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `tbl_demo_0`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_demo_0`;
CREATE TABLE `tbl_demo_0` (
  `id` int(11) NOT NULL  AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_demo_0
-- ----------------------------
INSERT INTO `tbl_demo_0` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `tbl_demo_1`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_demo_1`;
CREATE TABLE `tbl_demo_1` (
  `id` int(11) NOT NULL  AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_demo_1
-- ----------------------------
INSERT INTO `tbl_demo_1` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `tbl_demo_2`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_demo_2`;
CREATE TABLE `tbl_demo_2` (
  `id` int(11) NOT NULL  AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_demo_2
-- ----------------------------
INSERT INTO `tbl_demo_2` VALUES ('1', '1');

-- ----------------------------
-- Table structure for `tbl_demo_3`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_demo_3`;
CREATE TABLE `tbl_demo_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_demo_3
-- ----------------------------
INSERT INTO `tbl_demo_3` VALUES ('1', '1');


-- ----------------------------
-- Table structure for `tbl_user`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
      `id` int(11) NOT NULL,
      `name` varchar(45) DEFAULT NULL,
      `note` varchar(45) DEFAULT NULL,
      `create_date` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_user
-- ----------------------------
INSERT INTO `tbl_user` VALUES ('1', 'dogstar', 'oschina', '2015-12-01 09:42:31');
INSERT INTO `tbl_user` VALUES ('2', 'Tom', 'USA', '2015-12-08 09:42:38');
INSERT INTO `tbl_user` VALUES ('3', 'King', 'game', '2015-12-23 09:42:42');

