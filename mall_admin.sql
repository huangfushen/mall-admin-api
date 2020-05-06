/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 80019
Source Host           : localhost:3306
Source Database       : mall_admin

Target Server Type    : MYSQL
Target Server Version : 80019
File Encoding         : 65001

Date: 2020-05-04 12:23:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `authName` varchar(50) NOT NULL,
  `path` varchar(100) DEFAULT NULL,
  `children` varchar(50) DEFAULT NULL,
  `order` int NOT NULL,
  `father` int NOT NULL COMMENT '是否为父菜单 1是2否',
  `level` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES ('1', '用户管理', '/user', '2,3', '1', '0', '0');
INSERT INTO `menus` VALUES ('2', '用户列表', '/users', '7,8', '1', '1', '1');
INSERT INTO `menus` VALUES ('3', '测试列表', '/test', null, '2', '1', '1');
INSERT INTO `menus` VALUES ('4', '权限管理', '/auth', '5,6', '2', '0', '0');
INSERT INTO `menus` VALUES ('5', '权限列表', '/rights', null, '1', '4', '1');
INSERT INTO `menus` VALUES ('6', '角色列表', '/roles', null, '2', '4', '1');
INSERT INTO `menus` VALUES ('7', '添加用户', '/users/addUser', null, '1', '2', '2');
INSERT INTO `menus` VALUES ('8', '删除用户', '/users/delUser', null, '2', '2', '2');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roleName` varchar(50) NOT NULL,
  `roleDesc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', '超级管理员', '该系统最高权限管理员');
INSERT INTO `roles` VALUES ('2', '小黄', '最卑微的管理员');
INSERT INTO `roles` VALUES ('3', '张三', '打杂的');

-- ----------------------------
-- Table structure for role_auth
-- ----------------------------
DROP TABLE IF EXISTS `role_auth`;
CREATE TABLE `role_auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rid` int NOT NULL,
  `pid` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role_auth
-- ----------------------------
INSERT INTO `role_auth` VALUES ('93', '1', '1');
INSERT INTO `role_auth` VALUES ('94', '1', '2');
INSERT INTO `role_auth` VALUES ('95', '1', '3');
INSERT INTO `role_auth` VALUES ('98', '2', '1');
INSERT INTO `role_auth` VALUES ('99', '2', '2');
INSERT INTO `role_auth` VALUES ('102', '2', '8');
INSERT INTO `role_auth` VALUES ('103', '1', '7');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `salt` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `telphone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `role_name` varchar(30) DEFAULT NULL,
  `type` int DEFAULT NULL,
  `mg_state` varchar(20) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', 'a1689192bc30f4fd7e75638cdb25af8e', '12wGaKkN1hGuw', '15906022244', '1213885869@qq.com', '超级管理员', '1', 'true', '2020-04-29 03:42:24', '2020-05-04 12:13:27');
INSERT INTO `users` VALUES ('2', 'test', '447e96be090ca232eb97a1e7904a3254', '126D8rSh5sjUE', '15906022242', '1213885869@qq.com', '测试账号1', '1', 'true', '2020-04-29 04:09:53', '2020-04-29 08:45:02');
INSERT INTO `users` VALUES ('3', 'test1', '5608292e46587c895eb45d6d58c9c23e', '129orSYEyQMmA', '15906022244', '1213885869@qq.com', '测试账号2', '1', 'true', '2020-04-22 04:16:30', '2020-04-22 04:16:30');
INSERT INTO `users` VALUES ('5', 'test3', 'b8bfe89ebb3f92ff537db143aa644e63', '12/8YkHioExfE', '15906022245', '1213885869@qq.com', '测试账号4', '1', 'true', '2020-04-22 04:24:35', '2020-04-22 04:24:35');
INSERT INTO `users` VALUES ('7', 'test5', 'a96b11f83c0d208087bad0737360f6bf', '12vqchtOhiRzM', '15906022245', '1213885869@qq.com', '测试账号6', '1', 'true', '2020-04-23 05:01:09', '2020-04-23 05:01:09');
