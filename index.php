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
        $length    = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $lowercase = isset($_POST['lowercase']) ? (int)$_POST['lowercase'] : 2;
        $uppercase = isset($_POST['uppercase']) ? (int)$_POST['uppercase'] : 2;
        $digits    = isset($_POST['digits']) ? (int)$_POST['digits'] : 2;
        $specials  = isset($_POST['specials']) ? (int)$_POST['specials'] : 2;

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
        $service         = isset($_POST['service']) ? $_POST['service'] : '';
        $passwordToSave  = isset($_POST['password_to_save']) ? $_POST['password_to_save'] : '';
        $username        = isset($_POST['save_username']) ? $_POST['save_username'] : '';
        $user_password   = isset($_POST['save_user_password']) ? $_POST['save_user_password'] : '';

        if (!empty($service) && !empty($passwordToSave) && !empty($username) && !empty($user_password)) {
            $key = $user->getRawKey($username, $user_password);

            if ($key) {
                if ($passwordEntry->saveEncrypted($service, $passwordToSave, $key)) {
                    $passwordSavedMessage = "Encrypted password saved for $service.";
                } else {
                    $passwordSavedMessage = "Failed to save encrypted password.";
                }
            } else {
                $passwordSavedMessage = "Could not retrieve encryption key. Check login data.";
            }
        } else {
            $passwordSavedMessage = "All fields are required.";
        }
    }
}
?>

<!-- HTML FORMS -->
<h2>User Registration</h2>
<form method="POST">
    <label>Username: <input type="text" name="username" required /></label><br><br>
    <label>Password: <input type="password" name="password" required /></label><br><br>
    <button type="submit" name="register">Register</button>
</form>

<hr>

<h2>Password Generator</h2>
<form method="POST">
    <label>Password Length: <input type="number" name="length" value="10" required /></label><br><br>
    <label>Lowercase Letters: <input type="number" name="lowercase" value="2" required /></label><br><br>
    <label>Uppercase Letters: <input type="number" name="uppercase" value="2" required /></label><br><br>
    <label>Digits: <input type="number" name="digits" value="2" required /></label><br><br>
    <label>Special Characters: <input type="number" name="specials" value="2" required /></label><br><br>
    <button type="submit" name="generate">Generate Password</button>
</form>

<?php if (!empty($generatedPassword)): ?>
    <p><strong>Generated Password:</strong> <?php echo htmlspecialchars($generatedPassword); ?></p>
<?php endif; ?>

<hr>

<h2>Save Encrypted Password</h2>
<form method="POST">
    <label>Service Name: <input type="text" name="service" required /></label><br><br>
    <label>Password to Save: <input type="text" name="password_to_save" required /></label><br><br>
    <label>Your Username: <input type="text" name="save_username" required /></label><br><br>
    <label>Your Password: <input type="password" name="save_user_password" required /></label><br><br>
    <button type="submit" name="save_password">Save Password</button>
</form>

<?php if (!empty($passwordSavedMessage)): ?>
    <p><?php echo $passwordSavedMessage; ?></p>
<?php endif; ?>
