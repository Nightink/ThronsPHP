<?php
/**
 * Thorns框架应用程序类
 * 
 * @author Nightink
 * @date 2012-11-20
 * @package Core 核心包
 */
class App {
    
    // ++++++++start单例模型+++++++
    private static $_instance = null; // 类的实例

    private function __construct() {}
    //防止对象复制、克隆
    private function __clone() {}
    
    // 重写基类单例成员函数
    public static function getInstance() {
        // 判断单例是否存在 采用instanceof
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }  
    // +++++++++END单例++++++++++

    //应用初始化接口
    public function run() {
        $this->init();
        $this->parseR();
        $this->verifUser();
        $this->buildApp();
    }

    //框架初始化参数和配置
    private function init() {
        // 导入系统默认配置参数
        C(include TR_PATH . "/Commons/config.php");
        if(is_file(CONF_PATH.'conf.php')) {
            C(include CONF_PATH."conf.php");
        }
        //var_dump(C());
        /* 系统默认情况下开启session */
        if (C('DefaultSession')) {
            session_start();
        }
        //设定错误处理
        set_error_handler(array('Tr', 'trError'));
        //设定异常处理
        set_exception_handler(array('Tr', 'trException'));
        //注册自动加载函数
        //spl_autoload_register(array('Tr', 'autoload'));
        
        //设置时区
        if (!is_null(C('TimeZone')))
            date_default_timezone_set(C('TimeZone'));
        if(!defined('__ROOT__')) define('__ROOT__', '/'.ROOT_NAME); //当前网站根目录
        if(!defined('__APP__')) define('__APP__', __ROOT__.'/'.rtrim(APP_PATH, '/')); //应用项目路径
        if(!defined('__PUBLIC__')) define('__PUBLIC__', __APP__.'/Public'); //当前应用公共库定义宏
    }

    //路由控制调度
    private function parseR() {
        $router = Tr::getInstance('Router');
        $router->run();
        if(!defined('__URL__')) {
            $self_url = $_SERVER['PHP_SELF'];
            $self_url = substr($self_url, 0, strpos($self_url, '.php') + 4);
            if(!defined('__TR__')) define('__TR__', $self_url);
            $self_url .= '/'.C('CLASS');
            if(!defined('__URL__')) define('__URL__', /*C('Pact').$_SERVER['HTTP_HOST'].*/$self_url); //当前URL路由
        }
    }

    //用户控制、控制
    protected function verifUser() {
        if(C('UESR_DNS') && C('CLASS') !== C('NOT_C') && ! $_SESSION[C('USER_KEY')]) {
            $ver = explode('/', C('DEF_DNS'));
            //var_dump($ver);
            C('CLASS', $ver[0]);
            C('METHOD', $ver[1]);
            //echo '------';
        }
    }

    //调度控制器
    private function buildApp() {
        $class = C('CLASS').'Controller';
        //判断控制器类是否存在
        if(class_exists($class)){     //判断一下test.php这个文件中有没有User这个class
            $obj = new $class();
            $methodName = C('METHOD');
            if(method_exists($obj,$methodName)){   //判断一下User这个class中有有没有check这个方法
                $obj->$methodName();  //调用这个方法，结果是（我的名子叫tank）
            } else {
                $obj->errorMethod();
            }
        }
    }
}
?>