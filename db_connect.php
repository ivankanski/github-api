<?php
$user       = 'webuser';
$password   = 'webtest';
$dbname     = 'github_projects';
$host       = 'localhost';
$port       = 3306;

try {
    $dblink = new PDO("mysql:host=$host:$port;dbname=$dbname", $user, $password, array(
        PDO::ATTR_PERSISTENT => true));

} catch (PDOException $e) {
    echo 'Database Connection Error: ' . $e->getMessage() . '<br/>';
    exit;
}

