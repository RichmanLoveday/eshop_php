<?php
define("WEBSITE_TITLE", 'MY SHOP');
define('FLUTTER_PUBLIC_KEY', 'FLWPUBK_TEST-218ab7234657b306ff87836f5aa237cf-X');
define('FLUTTER_SECRET_KEY', 'FLWSECK_TEST-e11646b2407bef326f271a8eaf8b377c-X');

// database name
define('DB_NAME', "eshop_db");
define('DB_HOST_NAME', 'localhost');
define('DB_USER', "root");
define('DB_PASS', "");
define('DB_TYPE', 'mysql');

define('DEBUG', true);

if (DEBUG) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}

// for root url
$path = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$path = str_replace('index.php', '', $path);

define('ROOT', $path);
define("THEME", 'eshop/');
define('ASSETS', $path . "assets/");
define('IMAGES', $path . "assets/" . THEME . "/images/");
