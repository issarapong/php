<?php
// Database configuration
class DatabaseConfig {
    const HOST = 'mysql';
    const DB_NAME = 'php_lab_db';
    const USERNAME = 'php_user';
    const PASSWORD = 'php_password';
    const CHARSET = 'utf8mb4';
}

// PDO Database connection class
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DatabaseConfig::HOST . 
                   ";dbname=" . DatabaseConfig::DB_NAME . 
                   ";charset=" . DatabaseConfig::CHARSET;
                   
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DatabaseConfig::CHARSET
            ];
            
            $this->pdo = new PDO($dsn, DatabaseConfig::USERNAME, DatabaseConfig::PASSWORD, $options);
        } catch (PDOException $e) {
            throw new Exception("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // ป้องกัน clone
    private function __clone() {}
    
    // ป้องกัน unserialize
    private function __wakeup() {}
}

// Base Model class
abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function execute($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Database Error: " . $e->getMessage());
        }
    }
    
    public function findAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->execute($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->execute($sql, [$id]);
        return $stmt->rowCount() > 0;
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->execute($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
}

// Error Handler class
class ErrorHandler {
    public static function handle($error) {
        return [
            'success' => false,
            'message' => $error,
            'data' => null
        ];
    }
    
    public static function success($message = 'Operation successful', $data = null) {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}
?>