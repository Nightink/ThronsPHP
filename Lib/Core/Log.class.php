<?php
/**
 * Thorns框架实现系统日志类
 * 
 * @author Nightink
 * @date 2012-11-25
 * @package Core 核心包
 */
class Log extends Tr {

    // 日志级别 从上到下，由低到高
    const EMERG   = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT   = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT    = 'CRIT';   // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR     = 'ERR';    // 一般错误: 一般性错误
    const WARN    = 'WARN';   // 警告性错误: 需要发出警告的错误
    const NOTICE  = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO    = 'INFO';   // 信息: 程序输出信息
    const DEBUG   = 'DEBUG';  // 调试: 调试信息
    const SQL     = 'SQL';    // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志记录方式
    const SYSTEM = 0;
    const MAIL      = 1;
    const TCP       = 2;
    const FILE      = 3;

    //日期输出格式
    const FORMAT = "Y/m/d H:i:s" ;

    static $_logLevel = array( 
        'EMERG' => '严重错误', 
        'ALERT' => '警戒性错误',
        'CRIT' => '临界值错误', 
        'ERR' => '一般错误', 
        'WARN' => '警告性错误', 
        'NOTIC' => '通知',
        'INFO' => '信息', 
        'DEBUG' => '调试', 
        'SQL' => 'SQL语句'
    );

    static function write($message = '', $file_name = '', $type = self::FILE, $level = self::ERR) {
        if(empty($file_name)) {
            if(defined('LOG_PATH')) {
                echo LOG_PATH;//测试阶段使用
                $file_name = LOG_PATH . date('Y-m-d') . '.log';
            } else {
                echo 'LOG';
                $file_name = "./Log/" . date('Y-m-d') . '.log';
            }
        }
        if($type == self::FILE) {
            $file = fopen($file_name, 'a+');
            //date_default_timezone_set('PRC');//工程下单一入口文件index.php 直接修改当前日期
            $message = "[" . date(self::FORMAT) . "] [ " . self::$_logLevel[$level]. " ]" . $message; 
            //mktime()函数返回一个日期时间戳
            fwrite($file, $message."\r\n");
            fclose($file);
        }
    }
}
