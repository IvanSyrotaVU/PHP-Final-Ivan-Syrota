<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $bytes = openssl_random_pseudo_bytes(16);
        $rawKey = bin2hex($bytes);

        $encryptedKey = openssl_encrypt($rawKey, 'AES-128-ECB', $password);

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (username, password_hash, encryption_key) 
            VALUES (:username, :password, :encryption_key)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':encryption_key', $encryptedKey);

        return $stmt->execute();
    }
}
