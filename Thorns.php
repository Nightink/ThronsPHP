<?php
/**
 * Thorns框架入口文件
 *
 * @author Eyes
 * @date 2012-11-20
 */

if(version_compare(PHP_VERSION, '5.2.0', '<')) 
	exit('版本过低，无法使用本框架');	//判断用户PHP版本是否符合框架运行最低需求

if(!defined('TR_PATH')) define('TR_PATH', dirname(__FILE__).'/');	//用户未定义框架路径，自动补全
if(!defined('APP_PATH')) define('APP_PATH', './');
if(!defined('ROOT_NAME')) define('ROOT_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));

/* 
if (file_exists(APP_PATH.'Runtime/~runtime.php')) {
	require_once APP_PATH.'Runtime/~runtime.php';
} else {
	require_once TR_PATH.'Commons/runtime.php';
	createRunCache();
}
*/

//框架Debug阶段
require_once TR_PATH.'Commons/runtime.php';
createRunCache();

$app = App::getInstance();
$app->run();

?>