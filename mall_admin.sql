/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 80019
Source Host           : localhost:3306
Source Database       : mall_admin

Target Server Type    : MYSQL
Target Server Version : 80019
File Encoding         : 65001

Date: 2020-05-12 18:18:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cateName` varchar(50) NOT NULL,
  `father` int DEFAULT NULL,
  `children` text,
  `level` int NOT NULL,
  `cateDelete` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES ('1', '文学', null, '2,3,13', '1', 'true');
INSERT INTO `categories` VALUES ('2', '小说', '1', null, '2', 'true');
INSERT INTO `categories` VALUES ('3', '随笔', '1', null, '2', 'true');
INSERT INTO `categories` VALUES ('4', '流行', null, '5,6', '1', 'true');
INSERT INTO `categories` VALUES ('5', '漫画', '4', null, '2', 'true');
INSERT INTO `categories` VALUES ('6', '推理', '4', null, '2', 'true');
INSERT INTO `categories` VALUES ('7', 'test1', null, null, '1', 'true');
INSERT INTO `categories` VALUES ('9', 'test3', null, null, '1', 'true');
INSERT INTO `categories` VALUES ('11', 'test5', null, null, '1', 'true');
INSERT INTO `categories` VALUES ('12', '测测试', null, '13', '1', 'true');

-- ----------------------------
-- Table structure for cateparams
-- ----------------------------
DROP TABLE IF EXISTS `cateparams`;
CREATE TABLE `cateparams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attrName` varchar(50) NOT NULL,
  `cateId` int NOT NULL,
  `attrSel` varchar(50) NOT NULL,
  `attrWrite` varchar(50) DEFAULT NULL,
  `attrVals` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cateparams
-- ----------------------------
INSERT INTO `cateparams` VALUES ('1', 'test1', '2', 'only', null, '21');
INSERT INTO `cateparams` VALUES ('2', 'test2', '2', 'many', null, 'xzc xzc xzc');
INSERT INTO `cateparams` VALUES ('3', 'test3', '3', 'only', null, 'xzc');
INSERT INTO `cateparams` VALUES ('4', 'test4', '3', 'many', null, '');
INSERT INTO `cateparams` VALUES ('5', '电子书', '3', 'many', null, 'pdf html');

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `authName` varchar(50) NOT NULL,
  `path` varchar(100) DEFAULT NULL,
  `children` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `order` int DEFAULT NULL,
  `father` int NOT NULL COMMENT '是否为父菜单 1是2否',
  `level` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES ('1', '用户管理', '/user', '2,3', '1', '0', '0');
INSERT INTO `menus` VALUES ('2', '用户列表', '/users', '7,8,9,35,36', '1', '1', '1');
INSERT INTO `menus` VALUES ('3', '测试列表', '/test', '', '2', '1', '1');
INSERT INTO `menus` VALUES ('4', '权限管理', '/auth', '5,6', '2', '0', '0');
INSERT INTO `menus` VALUES ('5', '权限列表', '/rights', '10,11,24,26,27,28,12,31,37', '1', '4', '1');
INSERT INTO `menus` VALUES ('6', '角色列表', '/roles', '13,16,15,17,29,30,32,33,34', '2', '4', '1');
INSERT INTO `menus` VALUES ('7', '添加用户', 'User/addUser', null, '1', '2', '2');
INSERT INTO `menus` VALUES ('8', '删除用户', 'User/delUser', null, '2', '2', '2');
INSERT INTO `menus` VALUES ('9', '查看用户列表', 'User/getUserList', null, '3', '2', '2');
INSERT INTO `menus` VALUES ('10', '查看菜单列表', 'Menu/getMenulist', null, '1', '5', '2');
INSERT INTO `menus` VALUES ('11', '查看权限列表', 'Menu/getAllMenu', null, '2', '5', '2');
INSERT INTO `menus` VALUES ('12', '添加菜单权限', 'Menu/addMenu', null, '3', '5', '2');
INSERT INTO `menus` VALUES ('13', '查看角色列表', 'Role/getRoleRightList', null, '1', '6', '2');
INSERT INTO `menus` VALUES ('15', '获取单个角色信息', 'Role/getRoleRightById', null, '2', '6', '2');
INSERT INTO `menus` VALUES ('16', '获取权限列表(树形)', 'Role/getRightList', null, '3', '6', '2');
INSERT INTO `menus` VALUES ('17', '分配权限', 'Role/setRoleRight', null, '3', '6', '2');
INSERT INTO `menus` VALUES ('24', '获取各级权限', 'Menu/getLevelMenu', null, '1', '5', '2');
INSERT INTO `menus` VALUES ('26', '根据id获取权限', 'Menu/getMenuById', null, '5', '5', '2');
INSERT INTO `menus` VALUES ('27', '修改权限', 'Menu/updateMenu', null, '6', '5', '2');
INSERT INTO `menus` VALUES ('28', '删除菜单权限', 'Menu/delMenu', null, '7', '5', '2');
INSERT INTO `menus` VALUES ('29', '删除角色权限', 'Role/delRoleRight', null, '5', '6', '2');
INSERT INTO `menus` VALUES ('30', '获取单个角色权限', 'Role/getRoleRight', null, '6', '6', '2');
INSERT INTO `menus` VALUES ('31', '获取权限列表', 'Role/getRoleList', null, '7', '5', '2');
INSERT INTO `menus` VALUES ('32', '删除角色', 'Role/delRole', null, '8', '6', '2');
INSERT INTO `menus` VALUES ('33', '添加角色', 'Role/addRole', null, '9', '6', '2');
INSERT INTO `menus` VALUES ('34', '修改角色信息', 'Role/updateRole', null, '10', '6', '2');
INSERT INTO `menus` VALUES ('35', '修改用户信息', 'User/updateUser', null, '5', '2', '2');
INSERT INTO `menus` VALUES ('36', '获取单个用户信息', 'User/getUserById', null, '7', '2', '2');
INSERT INTO `menus` VALUES ('37', '获取角色三级权限id', 'Role/getThirdRightId', null, '12', '5', '2');
INSERT INTO `menus` VALUES ('43', '商品管理', '/Goods', ',44,45', '3', '0', '0');
INSERT INTO `menus` VALUES ('44', '商品分类', '/categories', null, '1', '43', '1');
INSERT INTO `menus` VALUES ('45', '分类参数', '/params', null, '4', '43', '1');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roleName` varchar(50) NOT NULL,
  `roleDesc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', '超级管理员', '该系统最高权限管理员');
INSERT INTO `roles` VALUES ('2', '小黄', '最卑微的管理员');
INSERT INTO `roles` VALUES ('3', '张三', '打杂的');
INSERT INTO `roles` VALUES ('10', 'test', '测试呀呀');

-- ----------------------------
-- Table structure for role_auth
-- ----------------------------
DROP TABLE IF EXISTS `role_auth`;
CREATE TABLE `role_auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rid` int NOT NULL,
  `pid` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role_auth
-- ----------------------------
INSERT INTO `role_auth` VALUES ('196', '1', '1');
INSERT INTO `role_auth` VALUES ('197', '1', '2');
INSERT INTO `role_auth` VALUES ('199', '1', '4');
INSERT INTO `role_auth` VALUES ('200', '1', '5');
INSERT INTO `role_auth` VALUES ('201', '1', '6');
INSERT INTO `role_auth` VALUES ('203', '1', '8');
INSERT INTO `role_auth` VALUES ('204', '1', '9');
INSERT INTO `role_auth` VALUES ('205', '1', '10');
INSERT INTO `role_auth` VALUES ('206', '1', '11');
INSERT INTO `role_auth` VALUES ('207', '1', '12');
INSERT INTO `role_auth` VALUES ('208', '1', '13');
INSERT INTO `role_auth` VALUES ('209', '1', '15');
INSERT INTO `role_auth` VALUES ('210', '1', '16');
INSERT INTO `role_auth` VALUES ('211', '1', '17');
INSERT INTO `role_auth` VALUES ('212', '1', '35');
INSERT INTO `role_auth` VALUES ('213', '1', '36');
INSERT INTO `role_auth` VALUES ('214', '1', '24');
INSERT INTO `role_auth` VALUES ('215', '1', '26');
INSERT INTO `role_auth` VALUES ('216', '1', '27');
INSERT INTO `role_auth` VALUES ('217', '1', '28');
INSERT INTO `role_auth` VALUES ('218', '1', '31');
INSERT INTO `role_auth` VALUES ('219', '1', '29');
INSERT INTO `role_auth` VALUES ('220', '1', '30');
INSERT INTO `role_auth` VALUES ('221', '1', '32');
INSERT INTO `role_auth` VALUES ('222', '1', '33');
INSERT INTO `role_auth` VALUES ('223', '1', '34');
INSERT INTO `role_auth` VALUES ('249', '2', '10');
INSERT INTO `role_auth` VALUES ('250', '2', '4');
INSERT INTO `role_auth` VALUES ('251', '2', '5');
INSERT INTO `role_auth` VALUES ('253', '2', '24');

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
INSERT INTO `users` VALUES ('1', 'admin', 'a1689192bc30f4fd7e75638cdb25af8e', '12wGaKkN1hGuw', '15906022244', '1213885869@qq.com', '超级管理员', '1', 'true', '2020-04-29 03:42:24', '2020-05-12 04:28:53');
INSERT INTO `users` VALUES ('2', 'test', '447e96be090ca232eb97a1e7904a3254', '126D8rSh5sjUE', '15906022242', '1213885869@qq.com', '小黄', '1', 'true', '2020-04-29 04:09:53', '2020-05-08 12:09:55');
INSERT INTO `users` VALUES ('3', 'test1', '5608292e46587c895eb45d6d58c9c23e', '129orSYEyQMmA', '15906022244', '1213885869@qq.com', '小黄', '1', 'true', '2020-04-22 04:16:30', '2020-04-22 04:16:30');
INSERT INTO `users` VALUES ('5', 'test3', 'b8bfe89ebb3f92ff537db143aa644e63', '12/8YkHioExfE', '15906022245', '1213885869@qq.com', '张三', '1', 'true', '2020-04-22 04:24:35', '2020-04-22 04:24:35');
INSERT INTO `users` VALUES ('7', 'test5', 'a96b11f83c0d208087bad0737360f6bf', '12vqchtOhiRzM', '15906022245', '1213885869@qq.com', '张三', '1', 'true', '2020-04-23 05:01:09', '2020-04-23 05:01:09');
