<?php
Class DBconfig {
    //Database configurations
    const host = "localhost";
    const username = "bytelove_main";
    const password = "){Zm&F.MBZc_";
    const name = "bytelove_main";
    public static function getConnection(){
        //Connection credentials
        $conn =  new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        return $conn;
    }
    public static function getPDO(){
        $dsn = 'mysql:host='.self::host.';dbname='.self::name;
        try {
            return new PDO($dsn, self::username, self::password);
        } 
        catch (PDOException $e) {
            return null;
        }
    }
    public static function getDB(){
         return new DB(self::name, self::username, self::password, self::host);
    }
}
class DB
{
    private $sql;
    private $mysql;
    private $result;
    private $result_rows;
    private $database_name;
    private static $instance;
    static $queries = array();
    function __construct($database_name, $username, $password, $host = 'localhost')
    {
        self::$instance = $this;

        $this->database_name = $database_name;
        $this->mysql = mysqli_connect($host, $username, $password, $database_name);
        $this->mysql->set_charset('utf8');

        if (!$this->mysql) {
            DBexeption::log('Database connection error: ' . mysqli_connect_error());
        }
    }
    final public static function instance($database_name = null, $username = null, $password = null, $host = 'localhost')
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database($database_name, $username, $password, $host);
        }

        return self::$instance;
    }
    private function _error($error)
    {
        DBexeption::log('Database error: ' . $error);
    }
   public function process_where($where, $where_mode = 'AND')
    {
        $query = '';
        if (is_array($where)) {
            $num = 0;
            $where_count = count($where);
            foreach ($where as $k => $v) {
                if (is_array($v)) {
                    $w = array_keys($v);
                    if (reset($w) != 0) {
                        throw new Exception('Can not handle associative arrays');
                    }
                    $query .= " `" . $k . "` IN (" . $this->join_array($v) . ")";
                } elseif (!is_integer($k)) {
                    $query .= ' `' . $k . "`='" . $this->escape($v) . "'";
                } else {
                    $query .= ' ' . $v;
                }
                $num++;
                if ($num != $where_count) {
                    $query .= ' ' . $where_mode;
                }
            }
        } else {
            $query .= ' ' . $where;
        }
        return $query;
    }
  public function select($table, $where = array(), $limit = false, $order = false, $where_mode = "AND", $select_fields = '*')
    {
        $this->result = null;
        $this->sql = null;

        if (is_array($select_fields)) {
            $fields = '';
            foreach ($select_fields as $s) {
                $fields .= '`' . $s . '`, ';
            }
            $select_fields = rtrim($fields, ', ');
        }

        $query = 'SELECT ' . $select_fields . ' FROM `' . $table . '`';
        if (!empty($where)) {
            $query .= ' WHERE' . $this->process_where($where, $where_mode);
        }
        if ($order) {
            $query .= ' ORDER BY ' . $order;
        }
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }
        return $this->query($query);
    }
    public function query($query)
    {
        self::$queries[] = $query;
        $this->sql = $query;

        $this->result_rows = null;
        $this->result = mysqli_query($this->mysql, $query);

        if (mysqli_error($this->mysql) != '') {
            $this->_error(mysqli_error($this->mysql));
            $this->result = null;
            return $this;
        }

        return $this;
    }
    public function sql()
    {
        return $this->sql;
    }
    public function result($key_field = null)
    {
        if (!$this->result_rows) {
            $this->result_rows = array();
            while ($row = mysqli_fetch_assoc($this->result)) {
                $this->result_rows[] = $row;
            }
        }

        $result = array();
        $index = 0;

        foreach ($this->result_rows as $row) {
            $key = $index;
            if (!empty($key_field) && isset($row[$key_field])) {
                $key = $row[$key_field];
            }
            $result[$key] = new stdClass();
            foreach ($row as $column => $value) {
                $this->is_serialized($value, $value);
                $result[$key]->{$column} = $this->clean($value);
            }
            $index++;
        }
        return $result;
    }
    public function result_array()
    {
        if (!$this->result_rows) {
            $this->result_rows = array();
            while ($row = mysqli_fetch_assoc($this->result)) {
                $this->result_rows[] = $row;
            }
        }
        $result = array();
        $n = 0;
        foreach ($this->result_rows as $row) {
            $result[$n] = array();
            foreach ($row as $k => $v) {
                $this->is_serialized($v, $v);
                $result[$n][$k] = $this->clean($v);
            }
            $n++;
        }
        return $result;
    }
   public function row($index = 0)
    {
        if (!$this->result_rows) {
            $this->result_rows = array();
            while ($row = mysqli_fetch_assoc($this->result)) {
                $this->result_rows[] = $row;
            }
        }

        $num = 0;
        foreach ($this->result_rows as $column) {
            if ($num == $index) {
                $row = new stdClass();
                foreach ($column as $key => $value) {
                    $this->is_serialized($value, $value);
                    $row->{$key} = $this->clean($value);
                }
                return $row;
            }
            $num++;
        }

        return new stdClass();
    }
    public function row_array($index = 0)
    {
        if (!$this->result_rows) {
            $this->result_rows = array();
            while ($row = mysqli_fetch_assoc($this->result)) {
                $this->result_rows[] = $row;
            }
        }

        $num = 0;
        foreach ($this->result_rows as $column) {
            if ($num == $index) {
                $row = array();
                foreach ($column as $key => $value) {
                    $this->is_serialized($value, $value);
                    $row[$key] = $this->clean($value);
                }
                return $row;
            }
            $num++;
        }

        return array();
    }
    public function count()
    {
        if ($this->result) {
            return mysqli_num_rows($this->result);
        } elseif (isset($this->result_rows)) {
            return count($this->result_rows);
        } else {
            return false;
        }
    }
   public function num($table = null, $where = array(), $limit = false, $order = false, $where_mode = "AND")
    {
        if (!empty($table)) {
            $this->select($table, $where, $limit, $order, $where_mode, 'COUNT(*)');
        }

        $res = $this->row();
        return $res->{'COUNT(*)'};
    }
    function table_exists($name)
    {
        $res = mysqli_query($this->mysql, "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '" . $this->escape($this->database_name) . "' AND table_name = '" . $this->escape($name) . "'");
        return ($this->mysqli_result($res, 0) == 1);
    }
    private function join_array($array)
    {
        $nr = 0;
        $query = '';
        foreach ($array as $key => $value) {
            if (is_object($value) || is_array($value) || is_bool($value)) {
                $value = serialize($value);
            }
            if($value === null) {
                $query .= ' NULL';
            } else {
                $query .= ' \'' . $this->escape($value) . '\'';
            }
            $nr++;
            if ($nr != count($array)) {
                $query .= ',';
            }
        }
        return trim($query);
    }

    function insert($table, $fields = array(), $appendix = false, $ret = false)
    {
        $this->result = null;
        $this->sql = null;

        $query = 'INSERT INTO';
        $query .= ' `' . $this->escape($table) . "`";

        if (is_array($fields)) {
            $query .= ' (';
            $num = 0;
            foreach ($fields as $key => $value) {
                $query .= ' `' . $key . '`';
                $num++;
                if ($num != count($fields)) {
                    $query .= ',';
                }
            }
            $query .= ' ) VALUES ( ' . $this->join_array($fields) . ' )';
        } else {
            $query .= ' ' . $fields;
        }
        if ($appendix) {
            $query .= ' ' . $appendix;
        }
        if ($ret) {
            return $query;
        }
        $this->sql = $query;
        $this->result = mysqli_query($this->mysql, $query);
        if (mysqli_error($this->mysql) != '') {
            $this->_error(mysqli_error($this->mysql));
            $this->result = null;
            return false;
        } else {
            return $this;
        }
    }
    function update($table, $fields = array(), $where = array(), $limit = false, $order = false)
    {
        if (empty($where)) {
            DBexeption::log('Where clause is empty for update method');
        }

        $this->result = null;
        $this->sql = null;
        $query = 'UPDATE `' . $table . '` SET';
        if (is_array($fields)) {
            $nr = 0;
            foreach ($fields as $k => $v) {
                if (is_object($v) || is_array($v) || is_bool($v)) {
                    $v = serialize($v);
                }
                if($v === null) {
                    $query .= ' `' . $k . "`=NULL";
                } else {
                    $query .= ' `' . $k . "`='" . $this->escape($v) . "'";
                }
                $nr++;
                if ($nr != count($fields)) {
                    $query .= ',';
                }
            }
        } else {
            $query .= ' ' . $fields;
        }
        if (!empty($where)) {
            $query .= ' WHERE' . $this->process_where($where);
        }
        if ($order) {
            $query .= ' ORDER BY ' . $order;
        }
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }
        $this->sql = $query;
        $this->result = mysqli_query($this->mysql, $query);
        if (mysqli_error($this->mysql) != '') {
            $this->_error(mysqli_error($this->mysql));
            $this->result = null;
            return false;
        } else {
            return $this;
        }
    }
  function delete($table, $where = array(), $where_mode = "AND", $limit = false, $order = false)
    {
        if (empty($where)) {
            DBexeption::log('Where clause is empty for update method');
        }
        $this->result = null;
        $this->sql = null;
        $query = 'DELETE FROM `' . $table . '`';
        if (!empty($where)) {
            $query .= ' WHERE' . $this->process_where($where, $where_mode);
        }
        if ($order) {
            $query .= ' ORDER BY ' . $order;
        }
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }
        $this->sql = $query;

        $this->result = mysqli_query($this->mysql, $query);
        if (mysqli_error($this->mysql) != '') {
            $this->_error(mysqli_error($this->mysql));
            $this->result = null;
            return false;
        } else {
            return $this;
        }
    }
    public function id()
    {
        return mysqli_insert_id($this->mysql);
    }
    public function affected()
    {
        return mysqli_affected_rows($this->mysql);
    }
    public function escape($str)
    {
        return mysqli_real_escape_string($this->mysql, $str);
    }
    public function error()
    {
        return mysqli_error($this->mysql);
    }
    private function clean($str)
    {
        if (is_string($str)) {
            if (!mb_detect_encoding($str, 'UTF-8', true)) {
                $str = utf8_encode($str);
            }
        }
        return $str;
    }
    public function is_serialized($data, &$result = null)
    {

        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        if (empty($data)) {
            return false;
        }
        if ($data === 'b:0;') {
            $result = false;
            return true;
        }
        if ($data === 'b:1;') {
            $result = true;
            return true;
        }
        if ($data === 'N;') {
            $result = null;
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if ($data[1] !== ':') {
            return false;
        }
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }

        $token = $data[0];
        switch ($token) {
            case 's' :
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
                break;
            case 'a' :
            case 'O' :
                if (!preg_match("/^{$token}:[0-9]+:/s", $data)) {
                    return false;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (!preg_match("/^{$token}:[0-9.E-]+;/", $data)) {
                    return false;
                }
        }

        try {
            if (($res = @unserialize($data)) !== false) {
                $result = $res;
                return true;
            }
            if (($res = @unserialize(utf8_encode($data))) !== false) {
                $result = $res;
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
    private function mysqli_result($res, $row, $field = 0)
    {
        $res->data_seek($row);
        $datarow = $res->fetch_array();
        return $datarow[$field];
    }
}
class DBexeption
{
    static function log($error){
        //Do nothing for now
    }
}
?>