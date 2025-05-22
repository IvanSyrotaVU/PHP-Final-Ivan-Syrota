<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $rawKey = bin2hex(openssl_random_pseudo_bytes(16));
        $encryptedKey = openssl_encrypt($rawKey, 'AES-128-ECB', $password);
        $encodedKey = base64_encode($encryptedKey);

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (username, password_hash, encryption_key) 
            VALUES (:username, :password, :encryption_key)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':encryption_key', $encodedKey);

        return $stmt->execute();
    }

    public function getRawKey($username, $plainPassword) {
        $stmt = $this->conn->prepare("SELECT encryption_key FROM {$this->table} WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $encoded = $row['encryption_key'];
            $encrypted = base64_decode($encoded);
            $raw = openssl_decrypt($encrypted, 'AES-128-ECB', $plainPassword);

            if (!$raw) {
                echo "<p style='color:red'>❌ Could not decrypt KEY.</p>";
            }

            return $raw;
        }

        return null;
    }
}


