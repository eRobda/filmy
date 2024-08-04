<?php
// profile_select.php
session_start();

if (!isset($_SESSION["UID"])) {
    header("Location: index.php");
    exit();
}

require_once 'backend/db.php';

// Get the single instance of the Database class
$db = Database::getInstance();

// Fetch profiles for the logged-in user
$userId = $_SESSION["UID"];
$profiles = $db->getProfilesByUserId($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vybrat profil</title>
</head>
<body>
<h2>Vyber profil</h2>
<?php if (!empty($profiles)): ?>
    <ul>
        <?php foreach ($profiles as $profile): ?>
            <li>
                <a href="set_profile.php?profile_id=<?php echo htmlspecialchars($profile['id']); ?>">
                    <?php echo htmlspecialchars($profile['jmeno_profilu']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<form method="POST" action="add_profile.php">
    <input type="text" name="profile_name">
    <button type="submit">Přidat profil</button>
</form>
<?php else: ?>
    <p>Nenašli se žádné profily. Kontaktujte podporu.</p>
<?php endif; ?>
<a href="logout.php">Odhlásit se</a>
</body>
</html>
