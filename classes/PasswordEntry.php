<?php
class PasswordEntry {
    private $conn;
    private $table = 'passwords';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function saveEncrypted($serviceName, $plainPassword, $encryptionKey) {

        $encryptedPassword = openssl_encrypt($plainPassword, 'AES-128-ECB', $encryptionKey);

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (service_name, password_value) 
            VALUES (:service_name, :password_value)");

        $stmt->bindParam(':service_name', $serviceName);
        $stmt->bindParam(':password_value', $encryptedPassword);

        return $stmt->execute();
    }
}
