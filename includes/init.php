<?php
session_start();

$databaseFile = __DIR__ . '/../config/database.php';
$helpersPath = __DIR__ . '/../functions/helpers.php';
if (!file_exists($helpersPath)) {
	die("Required file helpers.php is missing.");
}
require_once $helpersPath;
if (!file_exists($databaseFile)) {
    die('Database configuration file not found.');
}
require_once $databaseFile;