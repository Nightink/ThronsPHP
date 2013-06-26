<?php
/**
 * Thorns框架入口文件
 *
 * User: Nightink
 * Date: 13-6-26
 * Time: 上午11:46
 */

if(version_compare(PHP_VERSION, '5.2.0', '<')) 
    exit('版本过低，无法使用本框架');                                   //判断用户PHP版本是否符合框架运行最低需求

if(!defined('TR_PATH')) define('TR_PATH', dirname(__FILE__).'/');       //用户未定义框架路径，自动补全
if(!defined('APP_PATH')) define('APP_PATH', './');
if(!defined('ROOT_NAME')) define('ROOT_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
