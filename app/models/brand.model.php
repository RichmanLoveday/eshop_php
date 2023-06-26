<?php

use app\core\Database;
use app\core\Models;

class Brand extends Models
{
    public function get_active_brand()
    {

        $DB = Database::newInstance();
        $query = "SELECT * FROM brands WHERE disabled = '0' order by views desc";
        $brand = $DB->read($query);

        if (empty($brand)) return false;

        return $brand;
    }
}
