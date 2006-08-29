-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 28, 2006 at 02:40 PM
-- Server version: 4.1.18
-- PHP Version: 5.1.5
-- 
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sources`
-- 

CREATE TABLE `sources` (
  `entryno` bigint(20) NOT NULL auto_increment,
  `uniqueid` varchar(40) NOT NULL default '',
  `sourcefilename` varchar(100) NOT NULL default '',
  `address` text NOT NULL,
  PRIMARY KEY  (`entryno`),
  UNIQUE KEY `UNIQUE` (`address`(300))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6753 ;
