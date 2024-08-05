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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>
<body class="bg-neutral-950 flex flex-col items-center justify-center h-dvh w-dvw">
<?php if ($message): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
<img src="media/logo2.png" class="h-[5rem]" alt="">
<form method="POST" style="" action="">
    <p class="text-white mt-10" for="username">Uživatelské jméno:</p>
    <input class="py-2 px-3 outline outline-1 outline-white bg-transparent rounded text-white" type="text" id="username" name="username" required>
    <br><br>
    <p class="text-white" for="password">Heslo:</p>
    <input class="py-2 px-3 outline outline-1 outline-white bg-transparent rounded text-white" type="password" id="password" name="password" required>
    <br><br>
    <button class="outline outline-1 outline-white rounded py-2 px-3 text-white" type="submit">Přihlásit</button>
</form>
</body>
</html>
