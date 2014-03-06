<?php
	
	class Db_MySQLi {
		
		public $_db = false;
		public $func_log_err = 'classmysqli_log_err';
		public $func_log_quer = 'classmysqli_log_quer';
		
		private $db_data;
		
		
		function __construct() {
            
            include "connect_db.php";
            
			$this->db_data['server'] = $db_config['Database']['server'];
	        $this->db_data['username'] = $db_config['Database']['username'];
			$this->db_data['password'] = $db_config['Database']['password'];
			$this->db_data['database'] = $db_config['Database']['dbname'];
    
			$this->Connect();
		}
		
		function __destruct() {
			$this->Close();
		}
		
		
		function _error($method, $s = null, $e = false) {
			if(is_callable($this->func_log_err)) {
				if(!$e) $e = mysqli_error($this->_db);
				call_user_func_array($this->func_log_err, array($method, $s, $e));
			}
			trigger_error($method." ~~ ".$s." ~~ ".$e, E_USER_WARNING);
		}
		
		
		function Connect() {
			@mysqli_close($this->_db);
			$this->_db = mysqli_connect($this->db_data['server'], $this->db_data['username'], $this->db_data['password'], $this->db_data['database']);
			if(!$this->_db) {
				$this->_error(__METHOD__);
				return false;
			}
		}
		
		function Close() {
			@mysqli_close($this->_db);
			$this->_db = false;
		}
		
		function SelectDB($database) {
			$ret = mysqli_select_db($this->_db, $database);
			if($ret == false) {
				$this->_error(__METHOD__);
				return false;
			}
		}
		
		
		function Query($query) {
			$num_args = func_num_args();
			if($num_args > 1) {
				$args = func_get_args();
				$n = 1;
				$tq = explode('%', $query);
				for($i = 1, $qc = count($tq); $i < $qc; $i++) {
					if(empty($tq[$i]) || $num_args == $n) {
						$tq[$i] = '%'.$tq[$i];
						continue;
					}
					
					switch($tq[$i][0]) {
						case 'd':
							$tq[$i] = (int)$args[$n].substr($tq[$i], 1);
							$n++;
							break;
							
						case 'f':
							$tq[$i] = (float)$args[$n].substr($tq[$i], 1);
							$n++;
							break;
							
						case 's':
							$args[$n] = mysqli_real_escape_string($this->_db, $args[$n]);
							$tq[$i] = "'".$args[$n]."'".substr($tq[$i], 1);
							$n++;
							break;
							
						case 'a':
							if(!is_array($args[$n]))
								$tq[$i] = 'ERROR_ARRAY_EXPECTED'.substr($tq[$i], 1);
							
							$str = array();
							foreach($args[$n] as $key => $val) {
								if(!is_numeric($key))
									$name = '`'.$key.'` = ';
								else
									$name = '';
								
								if(is_numeric($val))
									$str[] = $name.$val;
								else{
									$val= mysqli_real_escape_string($this->_db, $val);
									$str[] = $name."'".$val."'";
								}
							}
							
							if(count($str))
								$tq[$i] = implode(',', $str).substr($tq[$i], 1);
							else
								$tq[$i] = substr($tq[$i], 1);
							
							$n++;
							break;
							
						default:
							$tq[$i] = '%'.$tq[$i];
					}//switch()
				}//for()
				
				$query = implode('', $tq);
			}
			
			if(is_callable($this->func_log_quer)) {
				call_user_func_array($this->func_log_quer, array($query));
			}
			
			$res = @mysqli_query($this->_db, $query);
			if($res == false) {
				$e = mysqli_error($this->_db);
				if($e == 'MySQL server has gone away') {
					$this->Connect();
					$res = @mysqli_query($this->_db, $query);
				}
				if($res == false) {
					$this->_error(__METHOD__, $query, $e);
					return false;
				}
			}
			
			if($res === true)
				return true;
			
			$ret = new MySQLiRes($res);
			return $ret;
		}
		
		function Fetch1($q) {
			$ret = false;
			$args = func_get_args();
			$r = call_user_func_array(array($this, 'Query'), $args);
			$r->Fetch(MYSQL_NUM);
			$ret = $r->Value(0);
			unset($r);
			return $ret;
		}
		
		function Fetch1r($q) {
			$ret = false;
			$args = func_get_args();
			$r = call_user_func_array(array($this, 'Query'), $args);
			$ret = $r->Fetch();
			unset($r);
			return $ret;
		}
		
		function Fetch1c($q) {
			$ret = false;
			$args = func_get_args();
            
            var_dump($args);
            
			$r = call_user_func_array(array($this, 'Query'), $args);
			while($r->Fetch(MYSQL_NUM)) {
				$ret[] = $r->Value(0);
			}
			unset($r);
			return $ret;
		}
		
		function Insert($tbl, $ar) {
			$row = array();
			$val = array();
			$ph = array();
			foreach($ar as $k => $v){
				$row[] = '`'.$k.'`';
				$val[] = $v;
				if(is_int($v))
					$ph[] = '%d';
				else
					$ph[] = '%s';
			}
			return call_user_func_array(array($this, 'Query'), array_merge(array('INSERT INTO `'.$tbl.'` ('.implode(', ', $row).') VALUES ('.implode(', ', $ph).')'), $val));
		}
		
		function Update($tbl, $ar, $where) {
			$args = func_get_args();
			unset($args[0], $args[2]);
			return call_user_func_array(array($this, 'Query'), array_merge(array('UPDATE `'.$tbl.'` SET %a WHERE '.$where), $args));
		}
		
		function Replace($tbl, $ar) {
			$row = array();
			$val = array();
			$ph = array();
			foreach($ar as $k => $v){
				$row[] = '`'.$k.'`';
				$val[] = $v;
				if(is_int($v))
					$ph[] = '%d';
				else
					$ph[] = '%s';
			}
			return call_user_func_array(array($this, 'Query'), array_merge(array('REPLACE INTO `'.$tbl.'` ('.implode(', ', $row).') VALUES ('.implode(', ', $ph).')'), $val));
		}
		
		function InsertID() {
			return mysqli_insert_id($this->_db);
		}
		
		function Ping() {
			return mysqli_ping($this->_db);
		}
		
		function Escape($s) {
			return '\''.mysqli_real_escape_string($this->_db, $s).'\'';
		}
		
	}
	
	
	
	class MySQLiRes {
		
		private $_res;
		private $_ans = array();
		
		function __construct($_res) {
			$this->_res = $_res;
		}
		
		function __destruct() {
			$this->Free();
			unset($this->_res);
			unset($this->_ans);
		}
		
		function __get($key) {
			return @$this->_ans[$key];
		}
		
		function __set($key,$val) {
			$this->_ans[$key] = $val;
		}
		
		function __isset($key) {
			return isset($this->_ans[$key]);
		}
		
		function __unset($key) {
			unset($this->_ans[$key]);
		}
		
		function Value($key) {
			return @$this->_ans[$key];
		}
		
		function Fetch($result_type = MYSQLI_ASSOC) {
			$this->_ans = mysqli_fetch_array($this->_res, $result_type);
			if(!$this->_ans) {
				$this->Free();
				return false;
			} else {
				return $this->_ans;
			}
		}
		
		function NumRows() {
			return mysqli_num_rows($this->_res);
		}
		
		function Free() {
			if(@mysqli_free_result($this->_res)){
				$this->_res = false;
				$this->_ans = array();
				return true;
			}
			return false;
		}
		
	}
	
?>