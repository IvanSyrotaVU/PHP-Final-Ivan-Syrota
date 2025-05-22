<?php
class PasswordEntry {
    private $conn;
    private $table = 'passwords';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save($serviceName, $password) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (service_name, password_value) 
            VALUES (:service_name, :password_value)");

        $stmt->bindParam(':service_name', $serviceName);
        $stmt->bindParam(':password_value', $password);

        return $stmt->execute();
    }
}

