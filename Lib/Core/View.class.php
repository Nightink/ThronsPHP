<?php
/**
 * Thorns框架实现View基类
 * 
 * @author Nightink
 * @date 2012-11-20
 * @package Core 核心包
 */
class View {
    //protected static $_viewObj;
    // 模版变量存储数据
    protected $_vars;
    // 模版名称
    protected $_tplName;
    // 编译文件路径
    protected $_parsePath;
    // 缓存文件路径
    protected $_cachePath;

    public function __construct() {
        $this->_vars = array();
    }
    
    private function __clone() {}
    
    // 解析页面模版函数
    protected function parseTpl($tplFileName) {
    }
    
    // 生成缓存文件
    protected function cacheFile() {
    }
    
    /**
     * 判断解析文件的有效性
     * 
     * @param String $tplFileName            
     * @return boolean ->true有效 false无效
     */
    protected function isEffectParse($tplFileName = '') {
        $this->_parsePath = CACHE_PATH . $tplFileName . '.tpl.php';
        if( !C('isParseTpl') ) {
            if (
                !file_exists ( $this->_parsePath ) 
                || filemtime ( $this->_tplName ) > filemtime ( $this->_parsePath )
            ) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 获得模版值    
     */
    public function get($key) {
        if(isset($this->_vars[$key])) {
            return $this->_vars[$key];
        } else {
            return false;
        }
    }
    
    /**
     * 注册模版变量
     * 
     * @param unknown_type $strKey            
     * @param unknown_type $value            
     */
    public function assign($strKey, $value = null) {
        if (is_array ( $strKey )) {
            $this->_vars = array_merge ( $this->_vars, $strKey );
        } else if (is_object ( $strKey )) {
            foreach ( $strKey as $key => $val ) {
                $this->_vars [$key] = $val;
            }
        } else {
            $this->_vars [$strKey] = $value;
        }
    }
    
    /**
     * 指定显示页面模版
     *
     * @param string $tplName            
     */
    public function display($tplName) {
        if(empty($tplName)) {
            //自动为空填充页面模版名称
            $tplName = C('CLASS').'/'.C('METHOD') . '.html';
            //设置页面缓存文件名
            $cacheName = C('CLASS') . '.' . C('METHOD');
        } else {
            if(!strpos($tplName, '/')) {
                $method = substr($tplName, 0, strpos($tplName, '.'));
                //定位模版路径
                $tplName = C('CLASS').'/'.$tplName;
                //设置页面缓存文件名
                $cacheName = C('CLASS') . '.' . $method;
            }
        }

        $this->_tplName = TPL_PATH . $tplName;
        //echo $this->_tplName;
        if (! file_exists ( $this->_tplName )) {
            exit ( $tplName . '模版文件不存在' );
        }
        //页面模版=解析 重点****
        if (! $this->isEffectParse ( $cacheName )) {
            include_once(TR_PATH . 'Lib/Tpl/Templates.class.php');
            $tpl = new Templates();
            //$this->_tplName = '';
            $tpl->compileTpl($this->_tplName, $this->_parsePath);
            //echo 'start parse!';
        }

        $this->output();
    }

    /** 页面输出 */
    protected function output() {
        ob_start();
        ob_implicit_flush(0);
        extract($this->_vars);
        include $this->_parsePath;
        $contents = ob_get_clean();
        echo $contents;
        exit();
    }
    
    //View类的析构函数
    public function __destruct() {
        //echo '对象将析构';
    }
}