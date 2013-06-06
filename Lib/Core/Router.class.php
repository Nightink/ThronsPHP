<?php
/**
 * Thorns框架系统URL路由解析，重装，跳转
 * 
 * @author Nightink
 * @date 2012-11-20
 * @package Core 核心包
 */
class Router extends Tr {
    //默认后缀扩展类型名
    const DEFAULT_EXT = 'php';
    //设置默认导航后缀文件名
    const DEFAULT_INDEX = 'index';
    //设置框架默认动作名
    //const DEFAULT_ACTION = 'Index';
    //设置框架默认模块名
    //const DEFAULT_MODULE = 'index';
    
    //对应响应的模块或者方法名
    //public $actionFunc = '';
    //对应相应的控制器类方法名
    //public $actionClass = '';
    
    /** 初始化传入URL字符串，读取相对应的动作和调用处理模块 */
    public function __construct() {    //路由解析类的构造函数----对于此核心工具类可能不需要进行对象化
        //$this->actionClass = empty($urlString) ? strtolower($urlString) : DEFAULT_ACTION;
    }
    
    private function __clone() {}
    
    /**
     * 获取原生路由
     */
    private function init() {
        //导入系统默认配置参数
        $_url = $_SERVER['REQUEST_URI'];
        if(strpos($_url, '.php') !== false) {
            ///ThornsProject/admin.php/User/add?uname=112&id=45
            $_p = strpos($_url, '?');
            //过滤混合模式下？get请求
            if($_p) {
                $_url = substr($_url, 0, $_p);
                //$param = substr($_url, $_p + 1);
                //$this->parseGet($param);
            }
        } else {
            $_url = '';
        }
        //echo $_url;
        return $_url;
    }
/*
    private function parseGet($param) {
        echo $param;
        var_dump($_GET);
    }
*/
    //本类方法需要进行重写
    protected function parseUrl() {
        //string 为 index.php/index/show
        //或者index.php/show 启动默认对应类型 按照对应的顺序
        $url = $this->init();
        //echo $url;
        if(empty($url)) {
            $className = C('DefaultController');
            $methodName = C('DefaultMethod');
        } else {
            preg_match("/(\w+\.php)/", $url, $match);        //查找php文件名

            $array = explode('/', $url);                   //将静态url进行分割

            $key = array_keys($array, $match[0]);                  //得到文件所对应的下标Array ( [0] => 2 )
            $file_array = array_slice($array, 0, $key[0]+1);      //Array ( [0] => [1] => test3 [2] => test.php )
            $param_array = array_slice($array, $key[0]+1);       //Array ( [0] => User [1] => check [2] => tank )
            $param = array_slice($param_array, 2);
            //针对ThornsProject/admin.php/User/add/uname/nan/id/4
            //这种TR里面常规模式进行参数重装 主要是针对GET请求方式
            resetGet($param);

            $file_path = implode('/', $file_array);
            
            //判断URL是否提交Class && method 
            $methodName = $param_array[1] ? $param_array[1] : C('DefaultMethod');
            //类名首字母大写
            $className = $param_array[0] ? $param_array[0] : C('DefaultController');
        }
        $className = ucwords($className);
        C('CLASS', $className); 
        C('METHOD', $methodName);
    }
    
    public function run() {
        $this->parseUrl();
    }

}

?>