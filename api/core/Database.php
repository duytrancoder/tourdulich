<?php
namespace Api\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    // Database configurations
    private $host = 'localhost';
    private $db_name = 'tour1'; // Change this if your database name is different
    private $username = 'root'; // Change if needed
    private $password = '';     // Change if needed
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            Response::error("Database connection failed: " . $exception->getMessage(), null, 500);
        }
    }

    /**
     * Get the database connection instance
     * @return PDO
     */
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
