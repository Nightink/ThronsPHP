<?php
/**
 * Thorns框架公共函数库
 * 主入口文件加载此函数库
 * 
 * @author Nightink
 * @date 2012-11-16
 */

//系统测试函数，针对疑问进行测试
function test($n = '') {
    static $name;    //静态变量，声明时不能进行赋值操作或者是初始化
    $name .= $n;
    echo $name.'<br/>';
}

/**
 * 系统自动加载类
 * 主要提供给应用层，用户不需引入类文件
 * @param string $classname
 */
function __autoload($classname) {
    if(substr($classname, -10) === 'Controller') { //Controller
        require_once LIB_PATH.'Controller/'.$classname.'.class.php';
    } elseif(substr($classname, -5) === 'Model') {    //Model
        require_once LIB_PATH.'Model/'.$classname.'.class.php';
    } else {
        return ;
    }
}

/**
 * 载入第三方工具类
 * hah/hhah 文件路径方式
 */
function __util($paths) {
    require_once UTIL_PATH.$paths;
}

/**
 * URL地址跳转
 */
function jumpUrl($url, $err_str = null) {
    if(is_string($url)) {
        if(!headers_sent()) {
            header("Location:{$url}");
            exit();
        } else {
            $head_str = "<meta http-equiv='Refresh' contect='0;url={$url}'>";
            exit($head_str);
        }
    } else {
        is_null($err_str) ? error_handler("{$url}地址字符串不合法，重新输入。") : error_handler("{$url}{$err_str}");
    }
}

/**
 * 设置或者取得系统配置值
 * @param mix $key
 * @param mix $value
 * @return void|multitype:|Ambigous <NULL, unknown>
 */
function C($key = '', $value = ''){
    
    static $_config = array();
    if(empty($key)) return $_config;    //如果参数为空，返回全部配置值
    
    if(!empty($value)) {
        if (is_string($key)) {
            $_config[$key] = $value;    //针对单一设置系统变量
            return ;
        }
    } else {
        if (is_array($key)) {
            //print_r($key);
            //只提供一维数组批量设置
            $_config = array_merge($_config, $key);        //批量设置系统配置值
        } else {
            return isset($_config[$key]) ? $_config[$key] : null;
        }
    }
}

/**
 * 将url转换成静态url路由,并返回新的url路由
 * @param string $_url
 * @param array $params
 * @param string $html
 * @param boolean $rewrite
 * @return string
 */
function U($_url, $params = array (), $html = "", $rewrite = true) {
    if ($rewrite) {        //开发阶段是不要rewrite,所在开发的时候，把$rewrite = false
        $url = ($_url == 'index') ? '' : '/' . $_url;
        if (! empty ( $params ) && is_array ( $params )) {
            $url .= '/' . implode ( '/', array_slice($params, 0 , 2));
            $param = array_slice($params, 2);
            foreach($param as $key => $value){
                $url .= '/' . $key . '/' . urlencode ( $value );
            }
        }
        
        if (! empty ( $html )) {
            $url .= '.' . $html;
        }
    } else {
        $url = ($_url == 'index') ? '/' : '/' . $_url;
        
        if (substr ( $url, - 4 ) != '.php' && $_url != 'index') {
            $url .= '.php';
        }
        
        if (! empty ( $params ) && is_array ( $params )) {
            $url .= '?' . http_build_query ( $params );
        }
    }
    return $url;
}

/**
 * 解析url,并载入相关类
 * @param string $_url
 * @return 
 */
function P($_url) {
    if(empty($_url)) return ;
    echo $_url.'<br />';
    preg_match("/(\w+\.php)/", $_url, $match);        //查找php文件名
    print_r($match);

    $array = explode('/', $_url);                   //将静态url进行分割
    echo '<br />';
    print_r($array);

    $key = array_keys($array, $match[0]);                  //得到文件所对应的下标Array ( [0] => 2 )
    $file_array = array_slice($array, 0, $key[0]+1);      //Array ( [0] => [1] => test3 [2] => test.php )
    $param_array = array_slice($array, $key[0]+1);       //Array ( [0] => User [1] => check [2] => tank )
    $param = array_slice($param_array, 2);
    resetGet($param);
    print_r($param_array);

    $file_path = implode('/', $file_array);
    $class = ucwords($param_array[0]).'Controller';
    if(class_exists($class)){     //判断一下test.php这个文件中有没有User这个class
        C('CLASS', $param_array[0]);    
        $obj = new $class();
        if(method_exists($obj,$param_array[1])){   //判断一下User这个class中有有没有check这个方法
            C('METHOD', $param_array[1]);
            $obj->$param_array[1]();  //调用这个方法，结果是（我的名子叫tank）
        }
    }
}

/**
 * GET请求，重装$_GET参数
 */
function resetGet($_param=null) {
    if(is_null($_param)) return;
    if(is_array($_param)) {
        $num = count($_param);
        if(!($num & 1)) {    //偶数
            for($i = 0; $i < $num; $i++) {
                $_GET[$_param[$i]] = $_param[++$i];
            }
        }
    }
}

/**
 * 系统错误输出函数
 * 
 * @param string $err_message        错误信息
 * @param unknown_type $err_level    错误级别
 */
function error_handler($err_message, $err_level = E_ERROR) {
    
    switch($err_level) {
    case E_ERROR:
        echo "<b>系统错误:</b> [{$err_level}] {$err_message}<br />";
        exit();
        break;
    case E_WARNING:
        echo "<b>系统警告:</b> [{$err_level}] {$err_message}<br />";
        break;
    }
    //if($err_level === E_ERROR) die();
}


//#++++++++++++++++++++++++++++++++++++++++++拓展函数部分++++++++++++++++++++++++++++++++++++++++++

/**
 * 中文或其他文字字符串翻转
 * @param string $str
 * @return string
 */
function reverse($str) {
    $ret = "";
    $len = mb_strwidth($str, "utf-8");
    for($i=0; $i< $len; $i++) {
        $arr[] = mb_substr($str, $i, 1, "utf-8");
    }
    return implode("", array_reverse($arr));
}


/**
 * 截取中文字符串，返回截取子字符串
 * 需要重写这个函数，针对中英文混合字符串进行长度判断，使得开发者人员在进行开发的时候，web页面整体美观（哈哈~）
 * @param $str
 * @param $s
 * @param $l
 * @return string
 */
function subzhstr($str, $s, $l) {
    $len = mb_strwidth($str, "utf-8");
    if($len <= $l)
        return $str;
    return mb_substr($str, $s, $l, "utf-8").'.....';
}

/**
 * 产生随机字符串函数
 * @param int $length
 * @return string
 */
function random($length) {
    $hash = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);    //mt_srand为mt_rand播下一个更好的随机数发生器种子
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 获得客户端IP
 * @return Ambigous <string, unknown>
 */
function GetIP(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}
?>
