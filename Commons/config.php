<?php
/**
 * Thorns框架系统默认配置参数
 *
 * @author Eyes
 * @date 2012-12-09
 */

return array ( 
	/* 普通配置 */
    'DefaultSession' => true, 
    'DefaultController' => 'Index', 
    'DefaultMethod' => 'index', 
    'TimeZone' => 'PRC',
	'isParseTpl' => true,

	/* 模版配置 */
	'JUMPERROR'     => TR_PATH.'Tpl/jumpUrl.html', // 默认错误跳转对应的模板文件
    'JUMPSUCCESS'   => TR_PATH.'Tpl/jumpUrl.html',  // 默认成功跳转对应的模板文件

	/* Ajax数据处理 */
	'DEFAULT_AJAX_RETURN' => 'JSON',

	/* http||https 协议 */
	'Pact' => 'http://', //默认为http://
			
	/* 数据库操作 */
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_USERNAME' => 'root',
	'DB_PASSWORD' => 'root',
	'DB_PORT' => '3306',
    'DB_MARK' => 'tr_',         //数据库前缀,默认为tr_
	'DB_NAME' => '',
	'DBCHARSET' => 'utf8',      //数据库的字符集编码

    /* 后台验证配置 */
    'USER_KEY' => 'uID',
    'UESR_DNS' => false,
    'DEF_DNS' => 'Public/login',    //默认设置
    'NOT_C' => 'Public'             //默认无需验证控制器
);

?>