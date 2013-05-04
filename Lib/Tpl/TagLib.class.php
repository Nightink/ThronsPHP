<?php
/**
 * Thorns框架模版标签类
 * --------------------------提供给开发者的一套标签库--------------------------------------
 * 
 * @author Eyes
 * @date 2012-12-8
 */
class TagLib {
	//文档树的资源标识
	protected $_dom = null; 
	//错误信息
	protected $_errorInfo = array(); 
	//标签库
	protected $_tags = array(
		'foreach' => array('attr' => 'from|item|key'),
		'if' => array('attr' => 'condition'),
		'elseif' => array('attr' => 'condition'),
		'else' => array('attr' => ''),
		'include' => array('attr' => 'file|type'),
		'print' => array('attr' => 'name|type'),
		'for' => array('attr' => 'start|end|loop'),
		'switch' => array('attr' => 'name'),
		'case' => array('attr' => 'name'),
		'default' => array('attr' => ''),
		'break' => array('attr' => ''),
		'continue' => array('attr' => ''),
		'while' => array('attr' => 'condition|loop|start'),
		'isset' => array('attr' => 'name'),
		'set' => array('attr' => 'name|value'),
		'flash' => array('attr' => 'id|class|value|width|height'),
		'page' => array('attr' => 'currentPage|totalPages|totalRecords|href|isAjax|pageRange')
	);
	//html符号化验证
	protected $_compExp = array(
		' neq ' => ' != ',
		' gte ' => ' >= ',
		' lte ' => ' <= ',
		' gt ' => ' > ',
		' lt ' => ' < ',
		' eq ' => ' == ',
		' and ' => ' && ',
		' or ' => ' || ',
		' ass ' => ' = '
	);
	protected $_tagCondition = ' neq | gte | lte | gt | lt | eq | and | or | ass ';
	//特殊的HTML标签，例如:<input />
	protected $_specialHtmlTags = array(
		'input','br','img','hr','link','meta'
	);
	//构造函数
	public function __construct() {}

	//foreach tag start parse
	protected function parseForeachStart() {
	}
	protected function parseForeachEnd() {
	}	//end foreach

	//if tag start parse
	protected function parseIfStart() {
	}
	protected function parseIfEnd() {
	}	//end if

	//for tag start parse
	protected function parseForStart() {		
	}
	protected function parseForEnd() {	
	}	//end for tag
	
	//Tag ->递归->parse
	protected function parseElement( $elements ) {
		// 判断是否子节点
		if( $elements->hasChildNodes() ) {
			foreach ($elements->childNodes as $key => $childNode) {
				if( $childNode->nodeType == XML_TEXT_NODE ) {
					continue;
				}
				//输出节点名称
				echo $childNode->nodeName." = ".$childNode->nodeValue."<br />";
				//判断节点是否有属性
				if( $childNode->hasAttributes() ) {
					foreach ($childNode->attributes as $attr) {
						echo $attr->nodeName." = ".$attr->nodeValue.'<br />';
					}
				}
				//递归解析元素的子节点
				parseElement( $childNode );
			}
		}
	}
	
	//element parse
	public function parse($file) {
		//echo $_htmlContents = file_get_contents($file);
		//XML解析
	}
	
}
?>