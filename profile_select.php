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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Vybrat profil</title>
</head>
<body class="bg-neutral-950 flex gap-10 h-dvh w-dvw items-center justify-center flex-col">
<?php if (!empty($profiles)): ?>
<h2 class="text-white font-semibold text-4xl">Kdo se dívá?</h2>
    <div class="flex gap-5">
        <?php foreach ($profiles as $profile): ?>
            <a class="text-neutral-500 flex items-center flex-col" href="set_profile.php?profile_id=<?php echo htmlspecialchars($profile['id']); ?>">
                <div class="flex flex-col items-center">
                    <div class="h-[9rem] w-[9rem] rounded bg-neutral-500">

                    </div>
                </div>
                <?php echo htmlspecialchars($profile['jmeno_profilu']); ?>
            </a>
        <?php endforeach; ?>
    </div>
<form method="POST" action="add_profile.php">
    <input type="text" class="py-2 rounded outline outline-1 bg-transparent outline-neutral-500 text-white px-3" name="profile_name">
    <button class="text-neutral-500 outline outline-neutral-500 outline-1 px-3 py-2 rounded" type="submit">Přidat profil</button>
</form>
<?php else: ?>
    <p class="text-white">Nenašli se žádné profily. Kontaktujte podporu.</p>
<?php endif; ?>
<a href="logout.php" class="outline outline-1 px-3 py-2 rounded outline-red-400 text-red-400 hover:bg-red-500 hover:shadow-[0_0px_60px_-10px_red] hover:text-white transition">Odhlásit se</a>
</body>
</html>
