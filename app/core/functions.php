<?php

use app\core\Database;

function show($data)
{
    echo "<pre>";
    print_r($data);
    echo "<pre>";
}

// Generate random string 
function get_random_string(int $lenght): string
{

    $array = ["*", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $text = '';
    for ($x = 0; $x < $lenght; $x++) {
        $random = rand(0, 62);
        $text .= $array[$random];
    }
    return $text;
}

function print_error(array $data, string $errType)
{
    if (isset($data['errors'][$errType])) {
        return "<div class=error>" . $data['errors'][$errType] . "</div>";
    }
}

function get_var(string $name, $default = NULL)
{
    if (isset($_POST[$name]) && !empty($_POST[$name])) {
        return $_POST[$name];
    } elseif (isset($_GET[$name]) && !empty($_GET[$name])) {
        return $_GET[$name];
    }
    return $default;
}

function selected(string $key, $value): string
{
    if (isset($_POST[$key]) && $_POST[$key] == $value) {
        return "selected";
    } elseif (isset($_GET[$key]) && $_GET[$key] == $value) {
        return "selected";
    }

    return '';
}

function checkbox($name, $value): string
{
    if (isset($_POST[$name]) && $_POST[$name] == $value) {
        return "checked";
    } elseif (isset($_GET[$name]) && $_GET[$name] == $value) {
        return "checked";
    }

    return "";
}

function esc($data)
{
    return addslashes($data);
}


function get_order_id()
{
    $orderid = 1;
    $DB = Database::newInstance();
    $query = "SELECT id FROM orders ORDER BY id DESC limit 1";
    $result = $DB->read($query);

    if (is_array($result)) {
        $orderid = $result[0]->id;
    }

    return $orderid;
}
