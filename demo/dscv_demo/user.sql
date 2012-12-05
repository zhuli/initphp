-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2012 年 11 月 27 日 05:30
-- 服务器版本: 5.1.28
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `test`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `user`
-- 

CREATE TABLE `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- 导出表中的数据 `user`
-- 

INSERT INTO `user` VALUES (1, '7777777', '8888888');
INSERT INTO `user` VALUES (2, '7777777', '8888888');
INSERT INTO `user` VALUES (3, '7777777', '8888888');
INSERT INTO `user` VALUES (4, '7777777', '8888888');
INSERT INTO `user` VALUES (5, '7777777', '8888888');
INSERT INTO `user` VALUES (6, '7777777', '8888888');
INSERT INTO `user` VALUES (7, '7777777', '8888888');
INSERT INTO `user` VALUES (8, '7777777', '8888888');
INSERT INTO `user` VALUES (9, '222222', '3333333');
