<?php
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/PasswordGenerator.php';
require_once 'classes/PasswordEntry.php';

$db = (new Database())->connect();
$user = new User($db);
$passwordEntry = new PasswordEntry($db);

$generatedPassword = '';
$passwordSavedMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate'])) {
        
        $length = (int)$_POST['length'];
        $lowercase = (int)$_POST['lowercase'];
        $uppercase = (int)$_POST['uppercase'];
        $digits = (int)$_POST['digits'];
        $specials = (int)$_POST['specials'];

        $gen = new PasswordGenerator($length, $lowercase, $uppercase, $digits, $specials);
        $generatedPassword = $gen->generate();
    } elseif (isset($_POST['register'])) {

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (!empty($username) && !empty($password)) {
            if ($user->register($username, $password)) {
                echo "User registered successfully!";
            } else {
                echo "Registration failed.";
            }
        } else {
            echo "All fields are required.";
        }
    } elseif (isset($_POST['save_password'])) {
        $service = isset($_POST['service']) ? $_POST['service'] : '';
        $passwordToSave = isset($_POST['password_to_save']) ? $_POST['password_to_save'] : '';

        if (!empty($service) && !empty($passwordToSave)) {
            if ($passwordEntry->save($service, $passwordToSave)) {
                $passwordSavedMessage = "Password saved for $service.";
            } else {
                $passwordSavedMessage = "Failed to save password.";
            }
        } else {
            $passwordSavedMessage = "Fill in both fields.";
        }
    }
}
?>

<h2>User Registration</h2>
<form method="POST">
    <label>
        Username:
        <input type="text" name="username" required />
    </label><br><br>

    <label>
        Password:
        <input type="password" name="password" required />
    </label><br><br>

    <button type="submit" name="register">Register</button>
</form>

<hr>

<h2>Password Generator</h2>
<form method="POST">
    <label>Password Length:
        <input type="number" name="length" value="10" required />
    </label><br><br>

    <label>Lowercase Letters:
        <input type="number" name="lowercase" value="2" required />
    </label><br><br>

    <label>Uppercase Letters:
        <input type="number" name="uppercase" value="2" required />
    </label><br><br>

    <label>Digits:
        <input type="number" name="digits" value="2" required />
    </label><br><br>

    <label>Special Characters:
        <input type="number" name="specials" value="2" required />
    </label><br><br>

    <button type="submit" name="generate">Generate Password</button>
</form>

<?php if (!empty($generatedPassword)): ?>
    <p><strong>Generated Password:</strong> <?= htmlspecialchars($generatedPassword) ?></p>
<?php endif; ?>

<hr>

<h2>Save Password</h2>
<form method="POST">
    <label>
        Service Name:
        <input type="text" name="service" required />
    </label><br><br>

    <label>
        Password to Save:
        <input type="text" name="password_to_save" required />
    </label><br><br>

    <button type="submit" name="save_password">Save Password</button>
</form>

<?php if (!empty($passwordSavedMessage)): ?>
    <p><?= $passwordSavedMessage ?></p>
<?php endif; ?>
