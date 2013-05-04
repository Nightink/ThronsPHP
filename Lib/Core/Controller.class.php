<?php
/**
 * Thorns框架实现Controller基类
 * 提供给应用程序实现C控制器，采用抽象类设计模式
 * public: 提供用户的接口; protected: 内部函数，用户透明;
 * 
 * @author Eyes
 * @date 2012-11-20
 * @package Core 核心包
 */
abstract class Controller {
	protected $_view = "";
	protected $name = '';

	public function __construct() {
		$this->_view = Tr::getInstance('View');
	}

	/**     
     * 获取当前控制器名称
     */
    protected function getActionName() {
        if(empty($this->name)) {
            // 获取控制器名称
            $this->name = substr(get_class($this),0,-6);
        }
        return $this->name;
    }
	
	//判断客户端是否是Ajax方式提交
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]))
            // 判断Ajax方式提交
            return true;
        return false;
    }
	
	protected function assign($strKey='', $value = NULL) {
		$this->_view->assign($strKey, $value);
	}
	
	//页面数据返回给客户端
	protected function display($tplName = '') {
		$this->_view->display($tplName);
	}

    /**
     * 操作错误跳转的快捷方法
     */
    protected function error($message,$jumpUrl='',$ajax=false) {
        $this->urlJump($message,0,$jumpUrl,$ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     */
    protected function success($message,$jumpUrl='',$ajax=false) {
        $this->urlJump($message,1,$jumpUrl,$ajax);
    }

    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     */
    private function urlJump($message,$status=1,$jumpUrl='',$ajax=false) {
        // 判断是否为AJAX返回
        if($ajax || $this->isAjax()) $this->ajaxReturn($ajax,$message,$status);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        //$this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        $this->assign('msgTitle',$status? 'Yes' : 'No');
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->view->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态

        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!$this->view->get('waitSecond'))    $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!$this->view->get('jumpUrl')) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('JUMPSUCCESS'));
        }else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!$this->view->get('waitSecond'))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!$this->view->get('jumpUrl')) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('JUMPERROR'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }

    protected function ajaxReturn($data,$info='',$status=1,$type='') {
        $result  =  array();
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        //扩展ajax返回数据, 在Action中定义function ajaxAssign(&$result){} 方法 扩展ajax返回数据。
        //if(method_exists($this,'ajaxAssign')) 
        //    $this->ajaxAssign($result);
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:text/html; charset=utf-8');
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($result));
        }else{
            // TODO 增加其它格式
        }
    }
    
    public function errorMethod() {
    	header('Content-Type:text/html; charset:utf-8');
    	echo '
<!DOCTYPE html>
<html>
<head>
    <title>系统加载模版错误</title>
    <meta charset="utf-8" />
    <title></title>
    <style type="text/css">
    	body{ background: #cff;}
        #error{
            border: 1px solid #0000ff; margin: auto; margin-top: 40px;
            width: 300px; height: 150px; line-height: 150px;
            border-radius: 5px; text-align: center;
        }
    </style>
</head>
<body>
    <div id="error">
        查找不到加载模块，请检查是否书写有误
    </div>
</body>
</html>
 			';
    }

}

?>