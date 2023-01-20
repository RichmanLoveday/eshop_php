<?php
define("WEBSITE_TITLE", 'MY SHOP');

// database name
define('DB_NAME', "eshop_db");
define('DB_USER', "root");
define('DB_PASS', "");

define('DEBUG', true);

if(DEBUG) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}

// for root url
$path = $_SERVER['REQUEST_SCHEME']. "://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$path = str_replace('index.php', '', $path);

define('ROOT', $path);
define('ASSETS', $path . "assets/");
define('IMAGES', $path . "assets/images/");

?>