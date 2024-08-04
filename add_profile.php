<?php
// set_profile.php
session_start();

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    exit();
}

if (!isset($_SESSION["UID"])) {
    header("Location: index.php");
    exit();
}

$profileName = $_POST['profile_name'];

// Set the profile ID in the session
require_once 'backend/db.php';
$db = Database::getInstance();
$db->createNewProfile($_SESSION["UID"], $profileName);

// Redirect to home.php
header("Location: profile_select.php");
exit();