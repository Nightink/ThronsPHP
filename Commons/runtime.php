<?php
/**
 * Thorns框架系统只在没有系统缓存情况下运行
 *
 * @author Nightink
 * @date 2012-12-6
 */

/**
 *  编译框架核心内容
 */
function createRunCache() {
    $paths = array ();
    $paths [] = TR_PATH . 'Commons/difines.php';
    $paths [] = TR_PATH . 'Commons/function.php';
    $core_paths = require_once TR_PATH . 'Commons/paths.php';
    $paths = array_merge ( $paths, $core_paths );
    
    $contents = '';
    foreach ( $paths as $path ) {
        require_once $path;
        $contents .= strFilter ( $path );
    }
    createDir ( array (
            APP_PATH,
            CONF_PATH,
            LIB_PATH,
            LIB_PATH . CONTROLLER_DIR,
            LIB_PATH . MODEL_DIR,
            TPL_PATH,
            RUNTIME_PATH,
            CACHE_PATH,
            PUBLIC_PATH
    ) );
    
    if (! file_exists ( LIB_PATH . 'Controller/IndexController.class.php' )) {
        file_put_contents ( LIB_PATH . 'Controller/IndexController.class.php', 
'<?php
class IndexController extends Controller {
    public function index() {
        echo \'<div style="width:300px; height:100px; margin:auto; margin-top:200px; 
                border:2px #999 solid; text-align:center; line-height:100px; color:#900;">
                欢迎您使用Thorns框架~</div>\';
    }
}
?>'        );
    }

    if(! file_exists(CONF_PATH . 'conf.php')) {
        file_put_contents(CONF_PATH . 'conf.php',
"<?php
return array(
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => 'root',
    'DB_PORT' => '3306',
    'DB_NAME' => '',
    'DBCHARSET' => 'utf8'
);
?>"            
        );
    }
    
    file_put_contents ( RUNTIME_PATH . '~runtime.php', "<?php" . $contents . '?>' );
    unset ( $contents );
}

/* 字符串过滤 */
function strFilter($path) {
    $content = file_get_contents ( $path );
    
    $content = str_replace ( '<?php', '', $content );
    $content = str_replace ( '?>', '', $content );
    // $content = trim($content);
    return $content;
}

/**
 * 批量创建文件夹
 *
 * @param mixde $dir_paths            
 * @param int $mode            
 */
function createDir($dir_paths = null, $mode = 0777) {
    if (is_null ( $dir_paths ))
        return;
    if (is_array ( $dir_paths )) {
        foreach ( $dir_paths as $dir_path ) {
            if (! is_dir ( $dir_path )) {
                echo $dir_path . '<br/>';
                mkdir ( $dir_path, $mode );
            }
        }
    }
}
?>