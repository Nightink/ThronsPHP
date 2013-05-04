<?php
/**
 * Thorns框架系统基类
 * 
 * 提供系统常用函数的命名空间，框架系统自定义处理函数
 *
 * @author Eyes
 * @date 2012-11-20
 * @package Core 核心包
 */
class Tr {
	//实现单例模式
	private static $_instance = array();
	
	/**
	 * 自动变量设置
	 *
	 * @param mixed $name 属性名称
	 * @param mixed $value 属性值
	 */
	public function __set($name, $value) {
		if (property_exists ( $this, $name ))
			$this->$name = $value;
	}
	
	/**
	 * 自动变量获取
	 *
	 * @param mixed $name 属性名称
	 * @return mixed
	 */
	public function __get($name) {
		return isset ( $this->$name ) ? $this->$name : null;
	}
	
	/**
	 * 
	 * @param string $function_name	类方法名称
	 * @param mixed $args			类方法所需参数
	 */
	public function __call($function_name, $args) {
		// 抛出异常
		//throw new TrException("无此方法$function_name");
		/** 此处不适合使用抛出异常的形式，针对不同子类抛出异常的行数都是相同指向父类Tr.class 所以应该采用php的错误处理机制，而不是异常处理 */
		
		if(!method_exists($this, $function_name)) {
			//error_handler("本类查无此函数$function_name");
			trigger_error('本类查无此方法，请检查书写是否有误', E_USER_ERROR);
		}
	}
	
	/**
	 * 系统自定义错误处理函数
	 */
	public static function trError($errno, $errstr, $errfile, $errline) {
		switch( $errno ) {
		case E_USER_ERROR:
		case E_ERROR:
			echo "<b>系统错误:</b>{$errstr}<br />";
			exit();
			break;
		case E_USER_WARNING:
		case E_WARNING:
			echo "<b>系统警告:</b>{$errstr}<br />";
			echo "<font color='red'>警告文件来源于：$errfile ，警告位置是：$errline 。</font>";
			break;
		}
	}
	
	/**
	 * 系统自定义异常处理函数
	 */
	public static function trException( Exception $e ) {
		echo $e->__toString();
	}
	
	/**
	 * ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * 实现类的单一模式，针对需要采用单例模式
	 * 针对单例模式，需要实现单例模式的子类需重写__clone()函数，防止实例对象被克隆
	 * 本方法具有缓存功能
	 *
	 * @param $class_name 类名        	
	 * @return mixed
	 */
	public static function getInstance($class_name = null) {
		if (is_array ( self::$_instance )) {
			if (! array_key_exists ( $class_name, self::$_instance )) {
				class_exists($class_name) ? 
					self::$_instance [$class_name] = new $class_name () : error_handler('系统查无此类');
			}
			return is_object ( self::$_instance [$class_name] ) ? self::$_instance [$class_name] : null;			
		}
		return NULL;
	}
}

?>
