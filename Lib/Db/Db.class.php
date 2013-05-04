<?php
/**
 * Thorns框架数据库操作基类
 * 
 * @author Eyes
 * @date 2012-11-20
 */
class Db {
	// 当前数据库
	public static $db;
	// 数据库服务器地址
	protected $dbLocalhost;
	// 数据库用户名
	protected $dbUsername;
	// 数据库用户密码
	protected $dbPassword;
	// 数据库名称
	protected $dbName;
	// 数据库字符集编码
	protected $dbCharset;
	// 数据库类型
	protected $dbType;
	// 查询个数
	protected $querynum = 0;
	
	// 当前操作的数据库连接
	protected $link = null;

	/**
	 * Db数据库的构造 取得Db数据库对象
	 */
	public function __construct(/*$host = "", $name = "", $password = "", $table = null, $dbCharset = null*/){
        $this->dbLocalhost = C('DB_HOST');
		$this->dbUsername = C('DB_USERNAME');
		$this->dbPassword = C('DB_PASSWORD');
		$this->dbName = C('DB_MARK').C('DB_NAME');
		$this->dbCharset = C('DBCHARSET');
		$this->link = $this->getConnect();
	}
	//防止对象复制
	public function __clone() {}

	protected function getConnect(){
		$conn = @mysql_connect($this->dbLocalhost,$this->dbUsername,$this->dbPassword ) or die('无法连接数据库服务器');
		mysql_query("set names '{$this->dbCharset}'");	//设置字符集编码
		$select_db = @mysql_select_db($this->dbName,$conn) or die('无法连接数据库');
	
		if($select_db){
			return $conn;
		}else{
			echo "数据库连接失败";
			exit();
		}
	}	
	
	//防止SQL语句注入
	public function escapeString($str) {
        return addslashes($str);
    }
    
    protected function _query($sql){
    	//$conn = $this->getConnect();
    	$query = mysql_query($sql,$this->link);
    	//$this->closeConnect($conn);
    	return $query;
    }
    
    public function insert($sql = '') {
    	//$sql = $this->escapeString($sql);
        file_put_contents('insert_sql.txt', $sql);
    	return $this->_query($sql);
    }
    
    public function delete($sql = '') {
    	//$sql = $this->escapeString($sql);
        file_put_contents('delete_sql.txt', $sql);
        return $this->_query($sql);
    }
    
    public function select($sql = '', $all = false) {
    	if(empty($sql)) {
    		echo $sql;
    	}
    	if($all) {
    		$menu = $this->fetch_array_all($sql);
    	} else {
    		$menu = $this->fetch_array_one($sql);
    	}
    	return $menu;
    } 

	public function update($sql = '') {
        //echo $sql;
        file_put_contents('update_sql.txt', $sql);
        return $this->_query($sql);
    }

    /**
     * 获得数据表数据总量
     * @param string $sql
     * @return resource
     */
    public function count($sql = '') {
        $rs =  $this->_query($sql);
        $num = mysql_num_rows($rs);
        file_put_contents('count.txt', $sql.' '.$num);
        return $num;
    }
	
	/**
	 * fetch_array($sql)函数用于获得当前行数据
	 */
	function fetch_array_one($sql){
		$list =  mysql_fetch_assoc(mysql_query($sql,$this->link));
		return $list;
	}
	
	/**
	 * select_sql($sql)函数用于获得查询的多个数据
	 */
	function fetch_array_all($sql){
		$rs = mysql_query($sql,$this->link);
		while($row = mysql_fetch_assoc($rs)){
			$menu[] = $row;
		}
		return $menu;
	}
	
	function fetch_page_query($sql){
		$rs = $this->query($sql);
		$num = mysql_num_rows($rs);
		return $num;
	}
	
	/**
	 * delete_update_sql($sql)函数用于增删改数据库数据
	 */
	function delete_update_sql($sql){
	
		$rs = $this->query($sql);
	
		return $rs;
	}
	
	function fn_insert($table,$name,$valen){
		$conn = $this->getConnect();
		$sql = "insert into $table ($name) vlan($valen)";
		$rs = $this->query($sql,$conn);
		$this->closeConnect($conn);
		return $rs;
	}


	/**
	 * 析构函数
	 */
	public function __destruct() {
		//释放查询
		if( $this->queryID ) {
			$this->free();
		}
		//关闭连接
		$this->close();
	}
	
	/**
	 * 关闭数据库，抽象函数由继承相应的数据库类书写
	 */
	protected function close() {}
}
?>