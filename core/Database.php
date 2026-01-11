<?php
class Database {
    private static $instance = null;
    private $dbh;

    private function __construct() {
        // DB credentials.
        if (!defined('DB_HOST')) {
            define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
        }
        if (!defined('DB_USER')) {
            define('DB_USER', getenv('DB_USER') ?: 'root');
        }
        if (!defined('DB_PASS')) {
            define('DB_PASS', getenv('DB_PASS') ?: '');
        }
        if (!defined('DB_NAME')) {
            define('DB_NAME', getenv('DB_NAME') ?: 'webdulich');
        }

        // Establish database connection.
        try
        {
            $this->dbh = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                DB_USER, 
                DB_PASS,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        }
        catch (PDOException $e)
        {
            error_log("Database connection error: " . $e->getMessage());
            exit("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.");
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->dbh;
    }
}
