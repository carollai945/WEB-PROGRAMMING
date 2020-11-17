<?php

/**
 * A base model for handling the database connections
 * @author jimmiw
 * @since 2012-07-02
 */
class Model
{
	protected $_dbh = null;
	protected $_table = "";
    protected $_primaryKey = "";
	
	public function __construct($table='',$primaryKey='id')
	{
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
		
		// starts the connection to the database
		$this->_dbh = new PDO(
			sprintf(
				"%s:host=%s;dbname=%s;port=%s",
				$settings['database']['driver'],
				$settings['database']['host'],
				$settings['database']['dbname'],
                $settings['database']['port']
			),
			$settings['database']['user'],
			$settings['database']['password'],
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
		$this->_table=$table;
		$this->init();
	}
	
	public function init()
	{
		
	}
	
	/**
	 * Sets the database table the model is using
	 * @param string $table the table the model is using
	 */
	protected function _setTable($table)
	{
		$this->_table = $table;
	}
	
	public function fetchOne($id, $islog = true)
	{
		$sql = 'select * from ' . $this->_table;
		$sql .= ' where id = ?';
        $params = array($id);
		$statement = $this->_dbh->prepare($sql);
		$statement->execute($params);
		
		if($islog){
			self::log($sql, $params);
		}		
		return $statement->fetch(PDO::FETCH_OBJ);
	}
	public function runQuery($proc, $params, $islog = true) 
	{
        $sql = $proc;
		$statement = $this->_dbh->prepare($sql);
		$statement->execute($params);

		if($islog){
			self::log($sql, $params);
		}		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}	

	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($data = array(), $islog = true)
	{
		$sql = '';
		
		$params = array();
		
		if (array_key_exists('id', $data)) {
			$sql = 'update ' . $this->_table . ' set ';
			
			$first = true;
			foreach($data as $key => $value) {
				if ($key != 'id') {
					$sql .= ($first == false ? ',' : '') . ' ' . $key . ' = ?';
					
					$params[] = $value;
					
					$first = false;
				}
			}
			
			// adds the id as well
			$params[] = $data['id'];
			
			$sql .= ' where id = ?';// . $data['id'];

			$statement = $this->_dbh->prepare($sql);
            $result = $statement->execute($params); 
            if($islog){
                self::log($sql, $params);
            }
            
			return $result;
		}
		else {
			$keys = array_keys($data);
			
			$sql = 'insert into ' . $this->_table . '(';
			$sql .= implode(',', $keys);
			$sql .= ')';
			$sql .= ' values (';
			
			$dataValues = array_values($data);
			$first = true;
			foreach($dataValues as $value) {
				$sql .= ($first == false ? ',?' : '?');
				
				$params[] = $value;
				
				$first = false;
			}
			
			$sql .= ')';

			$statement = $this->_dbh->prepare($sql);
			if ($statement->execute($params)) {
                $lastId = $this->_dbh->lastInsertId();

                if($islog){
                    self::log($sql, $params);
                }
				return $lastId;
			}
		}
		
		return false;
	}
	
	/**
	 * Deletes a single entry
	 * @param int $id the id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($id, $islog = true)
	{
        $sql = "delete from " . $this->_table . " where id = ?";
        $params = array($id);
		$statement = $this->_dbh->prepare($sql);
        $result = $statement->execute($params);
        
        if($islog){
            self::log($sql, $params);
        }
        
		return $result;
	}
    
    private function log($proc, $params)
    {      
        $ip = isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:(isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);
        $mid = isset($_SESSION["loginid"])?$_SESSION["loginid"]:"";
        $operation = $_SERVER['PHP_SELF'];
        $log_parms = array(
        	$ip,
        	$mid,	// user_id
        	$operation,	// operation
        	$proc,	// sql
        	serialize( $params )	// sp_parm
        );
        $sql = 'insert into web_access_log(`ip`,`user_id`,`operation`,`sql_query`,`sql_params`) values (?,?,?,?,?)';
        $statement = $this->_dbh->prepare($sql);
        $statement->execute($log_parms);

    }
}
