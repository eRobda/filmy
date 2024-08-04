<?php
// set_profile.php
session_start();

if (!isset($_SESSION["UID"]) || !isset($_GET['profile_id'])) {
    header("Location: index.php");
    exit();
}

$profileId = $_GET['profile_id'];

// Set the profile ID in the session
$_SESSION["PID"] = $profileId;

// Redirect to home.php
header("Location: home.php");
exit();
?>
