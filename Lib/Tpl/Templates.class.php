<?php
/**
 * Thorns框架实现View模版编译类
 * @author Eyes
 * 
 * 标签：arr数组遍历（支持二维数组、对象数组）,if,include
 * 变量支持： 普通变量、数组变量、对象变量
 * 过滤PHP脚本，简单的安全过滤
 *
 */
class Templates {
	
	public function __construct() {}
	
	private function __clone() {}	//禁止克隆复制
	
	protected function parseXML($attr) {
		$xml =  '<tpl><tag '.$attr.' /></tpl>';
		$xml = simplexml_load_string($xml);
		if(!$xml)
			echo 'XML解析失败';
		$xml = (array)($xml->tag->attributes());
		$array = array_change_key_case($xml['@attributes']);
		return $array;
	}
	
	/**
	 * 
	 * arr标签解析
	 * <arr name='' value='' key=''></arr>
	 * 		foreach($array $k => $v)
	 * 		name == array数组名称 ; value为$v别名; key为数组的键值(此属性为可选)
	 * 
	 */
	protected function parseArr($content) {
		$find = preg_match_all('/<arr\s(.+?)\s*>/is', $content, $matches);
		if($find) {
			for($i=0;$i<$find;$i++) {
				$attr = $matches[1][$i];
				$array = $this->parseXML($attr);
				$list = $array['name'];
				$value = $array['value'];
				$key = $array['key'];
				unset($array['name']);
				unset($array['value']);
				unset($array['key']);
				if(empty($key)) 
					$arrStr = '<?php if($'.$list.'): foreach($'.$list.' as $'.$value.'): ?>';
				else
					$arrStr = '<?php foreach($'.$list.' as $'.$key.' => $'.$value.'): ?>';
				//echo 'fag';
				$content = str_replace($matches[0][$i], $arrStr, $content);
			}
		}
		$content = preg_replace('/<\/arr>/is', '<?php endforeach; endif; ?>', $content);
		return $content;
	}

	/**
	 * 
	 * if标签解析
	 * 	condition 条件属性
	 * <if condition="false">
	 * 	        到了结婚年龄~
	 * <elseif condition="false">
	 *     还未到结婚年龄！！！！
	 * <else />
	 *     小P孩
	 * </if>  
	 * 
	 */
	protected function parseIf($content) {
		$find = preg_match_all('/<if\s(.+?)\s*>/is', $content, $matches);
		if($find) {
			for($i=0;$i<$find;$i++) {
				$attr = $matches[1][$i];
				$array = $this->parseXML($attr);
				$condition = $array['condition'];
				unset($array['condition']);
				$arrStr = '<?php if('.$condition.'): ?>';
				//echo 'fag';
				$content = str_replace($matches[0][$i], $arrStr, $content);
			}
		}
		unset($matches);
		$find = preg_match_all('/<elseif\s(.+?)\s*>/is', $content, $matches);
		if($find) {
			for($i=0;$i<$find;$i++) {
				$attr = $matches[1][$i];
				$array = $this->parseXML($attr);
				$condition = $array['condition'];
				unset($array['condition']);
				$arrStr = '<?php elseif('.$condition.'): ?>';
				//echo 'fag';
				$content = str_replace($matches[0][$i], $arrStr, $content);
			}
		}
		$content = preg_replace('/<else\s*\/>/is', '<?php else: ?>', $content);
		$content = preg_replace('/<\/if>/is', '<?php endif; ?>', $content);
		return $content;	
	}
	
	//解析Include标签
	protected function parseInclude($content) {
		// 读取模板中的布局标签
		$find = preg_match_all('/<include\s(.+?)\s*?\/>/is', $content, $matches);
		if($find) {
			for($i=0;$i<$find;$i++) {
				$attr = $matches[1][$i];
				$array = $this->parseXML($attr);
				$file  =  $array['file'];
				unset($array['file']);
				//echo 'fag';
				$content = str_replace($matches[0][$i], $this->parseIncludeItem($file,$array), $content);
			}
		}
		return $content;
	}
	//分析Include 属性
	protected function parseIncludeItem($tmplPublicName, $vars = array()) {
		if(substr($tmplPublicName,0,1) == '@')
			//支持加载变量文件名
		    //$tmplPublicName = $this->get(substr($tmplPublicName,1));
			$tmplPublicName = str_replace('@', rtrim(APP_PATH, '/'), $tmplPublicName);
		// 获取模板文件内容
		$parseStr = file_get_contents($tmplPublicName);
		//file_put_contents($tmplPublicName.'.path.txt', '测试');
		foreach ($vars as $key=>$val) {
			$parseStr = str_replace('['.$key.']',$val,$parseStr);
		}
		//再次对包含文件进行模板分析
		return $this->parseInclude($parseStr);
	}

	// parse Html page __PUBLIC__, __APP__, __URL__, __ROOT__ ...
	protected function parseDefPath( $content ) {
		//查选条件
		$search = array('__ROOT__', '__APP__', '__PUBLIC__', '__URL__', '__TR__');
		//替换字符
		$replace = array(
            '<?php echo __ROOT__; ?>',
            '<?php echo __APP__; ?>',
            '<?php echo __PUBLIC__; ?>',
            '<?php echo __URL__; ?>',
            '<?php echo __TR__; ?>'
        );
		//字符串查选、替换
		$content = str_replace($search, $replace, $content);
		return $content;
	}
	
	// parse {$var} : {$arr[tag]} : {$obj->name}
	protected function parseVar( $content ) {
		// 普通变量的正则查询、替换
		$content = preg_replace('/\{\s*\$([a-zA-Z_]\w*)\s*\}/i', '<?php echo $${1}; ?>', $content);
		// 关联数组变量的正则查询、替换
		$content = preg_replace('/\{\s*\$([a-zA-Z_]\w*)\[([a-zA-Z_]\w*)\]\s*\}/i', '<?php echo $${1}[${2}]; ?>', $content);
		// 对象变量的正则查询、替换
	 	$content = preg_replace('/\{\s*\$([a-zA-Z_]\w*)\.([a-zA-Z_]\w*)\s*\}/i', '<?php echo $${1}->${2}; ?>', $content);
		//返回模版内容
		return $content;
	}
	
	/**
	 * 编译W3C特殊字符
	 * 
	 */
	protected function parseComp($content) {
		$compExp = array(
			'{neq}' => '!=',
			'{gte}' => '>=',
			'{lte}' => '<=',
			'{gt}' => '>',
			'{lt}' => '<',
			'{eq}' => '==',
			'{and}' => '&&',
			'{or}' => '||',
			'{ass}' => '='
		);
		
		$content = str_replace(array_keys($compExp), array_values($compExp), $content);
		return $content;
	}
	
	/**
	 * 过滤模版文件中的<?php ?> or <??>代码植入
	 * 安全过滤函数
	 */
	protected function parsePhp($content) {
		$content =  preg_replace('/<\?(php)?[\s\S]*\?>/i', '', $content);
		return $content;
	}
	//编译View模版
	public function compileTpl( $file = '', $parsePath = '' ) {
		if(!file_exists($file)) {
			echo '模版加载文件不存在';
			return ;
		}
		//获取模版内容
		$content = file_get_contents ( $file );
		//解析Include标签
		$content = $this->parseInclude( $content );
		//过滤模版文件中PHP代码
		$content = $this->parsePhp($content);
		//file_put_contents ( 'a.txt', $content );
		//解析arr标签
		$content = $this->parseArr( $content );
		//解析if标签
		$content = $this->parseIf( $content );
		//解析w3c特殊字符
		$content = $this->parseComp( $content );
		//编译一般变量
		$content = $this->parseVar($content);
		//编译预定义路径宏
		$content = $this->parseDefPath($content);
		//$this->_parsePath = CACHE_PATH . $tplName . '.tpl.php';
		//写入缓存文件
		file_put_contents ( $parsePath, $content );
		//载入标签解析函数
		//include_once(TR_PATH . 'Lib/Tpl/TagLib.class.php');
		//$tags = new TagLib();
		//解析文件路径，方案：生成一个临时的模版文件进行模版解析，完成解析删除临时模版文件
		//$tags->parse( $parsePath );
	}
	
	//更新View模版缓存数据
	public function updataCache() {
		
	}
}
?>