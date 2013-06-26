<?php
/**
 * Thorns框架系统异常基类
 * 
 * @author Nightink
 * @date 2012-11-20
 */
class TrException extends Exception {
    
    public function __construct($message = null, /*$line = null,*/ $code = 0) {
        parent::__construct ( $message, $code );
        /*
        if (! is_null ( $line ))
            $this->line = $line;
        */
    }
    
    protected function StringFormat(Exception $e) {
        $e->__toString ();
    }

    public function trigger_error($errmsg='', $errtype=0) {
        $app = Yaf_Application::app();
        if ($app!=null) {
            $app->setErrorNo($errtype);
            $app->setErrorMsg($errmsg);
        }
        trigger_error($errmsg, $errtype);
    }
}
