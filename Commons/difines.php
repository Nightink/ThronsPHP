<?php
/**
 * Thorns框架公共路径定义
 * 框架核心包自动引入
 *
 * @author Nightink
 * @date 2012-11-16
 */

//应用程序生成文件名
define('LIB_DIR', 'Lib/');
define('RUNTIME_DIR', 'Runtime/');
define('CONF_DIR', 'Conf/');
define('TPL_DIR', 'Tpl/');
define('CONTROLLER_DIR', 'Controller/');
define('MODEL_DIR', 'Model/');
define('PUBLIC_DIR', 'Public/');

//应用程序文件路径
define('CONF_PATH', APP_PATH.CONF_DIR);
define('RUNTIME_PATH', APP_PATH.RUNTIME_DIR);
define('LIB_PATH', APP_PATH.LIB_DIR);
define('TPL_PATH', APP_PATH.TPL_DIR);
define('CACHE_PATH', RUNTIME_PATH.'Cache/');
define('PUBLIC_PATH', APP_PATH.PUBLIC_DIR);

/* Thorns 框架系统路径 */
define('UTIL_PATH', TR_PATH.'Util/');

?>