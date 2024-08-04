<?php
// set_profile.php
session_start();

if (!isset($_SESSION["UID"]) || !isset($_SESSION["PID"])) {
    header("Location: index.php");
    exit();
}

require_once 'backend/db.php';
$db = Database::getInstance();

$db->setSeriesWatchTime($_SESSION["PID"], $_GET["serialId"], $_GET["serie"], $_GET["epizoda"], $_GET["cas"], $_GET["celkovy_cas"]);


