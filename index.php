<?php
require_once 'config/database.php';
require_once 'classes/User.php';

$db = (new Database())->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    echo $username;
    echo $password;
    if (!empty($username) && !empty($password)) {
        if ($user->register($username, $password)) {
            echo "User registered successfully";
        } else {
            echo "Registration failed.";
        }
    } else {
        echo "Fill in all fields please.";
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required /><br><br>
    <input type="password" name="password" placeholder="Password" required /><br><br>
    <button type="submit">Register</button>
</form>