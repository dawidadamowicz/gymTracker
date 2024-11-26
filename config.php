<?php

require_once __DIR__ . '/vendor/autoload.php';  // Ensure the autoload file is included

use Dotenv\Dotenv;

class Config {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct() {
        // Load the .env file and set up the environment variables
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // Assign values from environment variables to the class properties
        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];

        // Connect to the database
        $this->connect();
    }

    public function connect() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
