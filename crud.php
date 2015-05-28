<?php
class Crud
{
    
    private $connection;
    private $host;
    private $database;
    private $db_username;
    private $db_password;
    
    function __construct($host = 'localhost', $database, $db_username, $db_password) {
        if (!$database) {
            die('NO DATABASE PROVIDED');
        }
        $this->host = $host;
        $this->database = $database;
        $this->db_username = $db_username;
        $this->db_password = $db_password;
        $this->connection = $this->pdoConnect();
    }
    
    function pdoConnect() {
        try {
            $conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=utf8', $this->db_username, $this->db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    function getTableData($table_name) {
        $query = $this->connection->query(' SELECT * FROM ' . $table_name);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getRow($table_name, $table_id , $where_column = 'id') {
        $stmt = $this->connection->prepare("SELECT * FROM $table_name WHERE $where_column=$table_id LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function getTableColumnNames($table_name) {
        $query = $this->connection->prepare("DESCRIBE " . $table_name);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
    
    function insertRow($table_name, $values) {
        foreach ($values as $field => $v) $ins[] = ':' . $field;
        $ins = implode(',', $ins);
        $fields = implode(',', array_keys($values));
        $sql = "INSERT INTO $table_name ($fields) VALUES ($ins)";
        $sth = $this->connection->prepare($sql);
        foreach ($values as $f => $v) {
            $sth->bindValue(':' . $f, $v);
        }
        $sth->execute();
        return $this->connection->lastInsertId();
    }
    
    function updateRow($table_name, $updates, $id ,$where_column = 'id') {
/*        $updates = array_filter($updates, function ($value) {
            return null !== $value;
        });*/
        
        $query = 'UPDATE ' . $table_name . ' SET';
        $values = array();
        
        foreach ($updates as $name => $value) {
            $query.= ' ' . $name . ' = :' . $name . ',';
            // the :$name part is the placeholder, e.g. :zip
            $values[':' . $name] = $value;
            // save the placeholder            
        }
        $values[':id'] = (int)$id;
        


        $query = substr($query, 0, -1) . ' WHERE '.$where_column.' = :id ;';
        // remove last , and add a ;       
        $sth = $this->connection->prepare($query);
        $sth->execute($values);
        
        // bind placeholder array to the query and execute everything
        
        // ... do something nice :)
        return $sth->rowCount();
    }

    function deleteRow($table_name,$row_id){
		$sql = "DELETE FROM $table_name WHERE id = :id";
		$stmt = $this->connection->prepare($sql);
		$stmt->bindParam(':id', $row_id, PDO::PARAM_INT);   
		$stmt->execute();
    }


}
?>