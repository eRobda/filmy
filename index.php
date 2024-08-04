<?php
//index.php
// Include the Database class
require_once 'backend/db.php';

// Initialize variables
$message = "";
$userId = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Get the single instance of the Database class
    $db = Database::getInstance();

    // Check login credentials
    $userId = $db->login($username, $password);
    if ($userId) {
        $message = "Login successful!";
        session_start();
        $_SESSION["UID"] = $userId;
        header("Location: profile_select.php");
    } else {
        $message = "Login failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
