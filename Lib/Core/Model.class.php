<?php
/**
 * Thorns框架实现Model基类
 * 实现ORM
 * 
 * @author Nightink
 * @date 2012-11-20
 * @package Core 核心包
 */
class Model {
    // 当前数据库操作对象
    protected $db = null;
    // 数据库名称
    protected $dbName = '';
    // 数据表名
    protected $dbTabName = '';
    // 模型名称
    protected $name = '';
    // 操作数据
    protected $options = array();
    // 进行操作字段数组
    protected $field = array();
    // 数据库查询语句
    protected $sql = '';
    // 上一次操作SQL语句
    protected $prevsql = '';
    // 触发操作where,OrderBy,like,limit 状态标识
    protected $status = '';

    public function __construct($dbTabName = '') {
        $this->dbTabName = C('DB_MARK').$dbTabName;
        //$this->db = new Db();
        $this->db = Tr::getInstance('Db');
    }

    /**
     * 解析配置参数
     * @param Array || Object $obj
     * @return boolean
     */
    protected function parseOptions($obj = array()) {
        //针对参数进行处理 => 转化成 关联数组
        if(is_array($obj)) {
            $this->options = $obj;
        } else if(is_object($obj)) {
            $this->options = get_object_vars($obj);
        } else {
            return false;
        }
        //获得配置参数和对应数据库字段
        if(!empty($this->options)) {
            $this->field = array_keys($this->options);
        }
        return true;
    }

    // 取得数据为数组{ 1.索引数组 2.关联数组    }
    protected function getArr() {}

    // 取得数据为对象
    protected function getObj() {}

    /**
     * where 语句 用于进行增删改查的链式操作
     * @param Array $arr
     * @param 可选  $factor 默认为 and
     * @return Model
     */
    public function where($arr = null, $factor = 'AND') {
        if(is_array($arr)) {
            if(is_string($factor)) {
                $this->status .= ' WHERE';
                foreach ($arr as $k => $v) {
                    $this->status .= " {$v[0]} {$v[2]} {$this->isString($v[1])} $factor";
                }
                $this->status = rtrim($this->status, $factor);
            }
        }
        return $this;
    }

    //Order by 语句
    public function orderBy($arr) {
        $this->status .= ' ORDER BY ';
        if(is_array($arr)) {
            foreach($arr as $name => $val) {
                $this->status .= "{$name} {$val},";
            }
            file_put_contents('order.txt', $this->status);
            $this->status = rtrim($this->status, ',');
        } else {
            trigger_error('ORDER BY参数必须为数组', E_USER_ERROR);
        }
        return $this;
    }

    //like 语句
    public function like() {

        return $this;
    }

    /**
     * 实现limit语句分段获得数据
     *
     * @param int $start
     * @param int $length
     * @return Model
     */
    public function limit($start = 0, $length) {
        if(is_int($length)) {
            $this->status .= " LIMIT {$start},{$length}";
        } else {
            trigger_error('limit参数不为整数', E_USER_ERROR);
        }
        return $this;
    }

    /**
     * Model模型的select操作
     * select (*) from dbtabname where
     * @param Array || Object $obj
     * @param Array || NULL   $factor
     * @return boolean|multitype:
     */
    public function select($all, $factor = NULL, $obj = NULL) {
        if(!is_null($obj)) {
            if(!$this->parseOptions($obj)) {
                return false;
            }
        }

        $this->sql .= "SELECT ";
        //factor 不为空为： select需要获取几个字段 ；为空：*
        if(is_array($factor)) {
            $this->sql .= implode(',', $factor);
        } else {
            $this->sql .= "* FROM {$this->dbTabName}";
        }
        if(!empty($this->status)) {
            $this->sql .= $this->status;
        }
        file_put_contents('select.txt', $this->sql);
        $list = $this->db->select($this->sql, $all);
        //var_dump($list);
        $this->clearOptions();
        return $list;
    }

    /**
     * 更新保存
     */
    public function save($obj = array(), $factor = NULL) {
        if(!$this->parseOptions($obj)) {
            return false;
        }
        //update语句拼接
        $this->sql .= "UPDATE {$this->dbTabName} SET ";
        $temp = '';
        foreach ($this->field as $term) {
            //针对字段类型为字符型的做单引号处理
            $temp .= "`{$term}` = {$this->isString($this->options[$term])},";
        }
        $this->sql .= rtrim($temp, ',');
        if(is_array($factor)) {
            $this->sql .= ' WHERE ';
            $temp = '';
            foreach($factor as $k => $v) {
                $temp .= "`{$k}` = {$this->isString($v)}";
            }
            $this->sql .= $temp;
        }
        $falg = $this->db->update($this->sql);
        $this->clearOptions();
        return $falg;
    }

    /**
     * Model模型的insert操作
     * @param Array || Object $obj $obj
     * @return int
     */
    public function insert( $obj = array() ) {
        if(!$this->parseOptions($obj)) {
            return false;
        }
        //insert语句拼接
        $this->sql .= "INSERT INTO {$this->dbTabName}(";
        $this->sql .= implode(',', $this->field).") VALUES (";
        $temp = '';
        foreach ($this->field as $term) {
            //针对字段类型为字符型的做单引号处理
            $temp .= "{$this->isString($this->options[$term])},";
        }
        $this->sql .= rtrim($temp, ',').');';
        //调用数据库执行语句
        $falg = $this->db->insert($this->sql);
        $this->clearOptions();
        return $falg;
    }

    protected function isString($var) {
        if(is_string($var)) {
            $temp = "'{$var}'";
        } else {
            $temp = "{$var}";
        }
        return $temp;
    }

    /**
     * Model模型的delete操作
     * @param  Array || Object $obj
     * @param  String || NULL $factor
     * @return int
     */
    public function delete($obj = array(), $factor = NULL ) {
        if(!func_num_args()) {
            $this->sql = "DELETE FROM {$this->dbTabName};";
            //return $this->sql;
        } else {
            if(!$this->parseOptions($obj)) {
                return false;
            }
            if(is_null($factor)) {
                $factor = '';
            }
            $this->sql = "DELETE FROM {$this->dbTabName} WHERE";
            //$this->sql .= "(".implode(',', $this->field).") value(";
            $temp = '';
            foreach ($this->field as $term) {
                //针对字段类型为字符型的做单引号处理
                $temp .= " {$term} = {$this->isString($this->options[$term])} $factor";
            }
            $this->sql .= rtrim($temp, $factor);
            //$this->sql .= ');';
        }
        $falg = $this->db->delete($this->sql);
        $this->clearOptions();
        return $falg;
    }

    /**
     * 统计当前数据模型的数据容量
     * 用于分页查询
     */
    public function count($sql = '') {
        if(!func_num_args()) {
            $this->sql = "SELECT * FROM {$this->dbTabName}";
        } else {
            $this->sql = $sql;
        }
        if(!empty($this->status)) {
            $this->sql .= $this->status;
        }
        $count = $this->db->count($this->sql);
        $this->sql = '';
        $this->status = '';
        return $count;
    }

    /**
     * 清除当前参数配置数据
     */
    protected function clearOptions() {
        $this->options = array();
        $this->prevsql = $this->sql;
        $this->sql = '';
        $this->status = null;
    }
}

?>