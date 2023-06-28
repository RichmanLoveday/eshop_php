<?php

use app\core\Database;
use app\core\Models;

class Search extends Models
{
    public static function get_categories(string $name)
    {
        $DB = Database::newInstance();

        $query = "SELECT id, category FROM categories WHERE disabled = 0 order by views desc";
        $data = $DB->read($query);

        // loop through and echo data
        if (is_array($data)) {
            foreach ($data as $row) {
                echo "<option " . selected($name, $row->id) . " value=" . $row->id . ">" . $row->category . "</option>";
            }
        }
    }


    public static function get_years(string $name)
    {
        $DB = Database::newInstance();

        $query = "SELECT id, date FROM products group by year(date)";
        $data = $DB->read($query);

        // loop through and echo data
        if (is_array($data)) {
            foreach ($data as $row) {
                echo "<option  " . selected($name, $row->id) . " value=" . $row->id . ">" . date("Y", strtotime($row->date)) . "</option>";
            }
        }
    }

    public static function get_brands()
    {
        $DB = Database::newInstance();

        $query = "SELECT id, brand FROM brands WHERE disabled = 0 order by views desc";
        $data = $DB->read($query);

        // loop through and echo datna
        if (is_array($data)) {
            $num = 0;
            foreach ($data as $row) {
                echo "<div style=\"display:inline-block; margin-right:10px;\">
                        <input  " . checkbox('brand-' . $num, $row->id) . " id=\"$row->id\" value=\"$row->id\" name=\"brand-$num\" type=\"checkbox\">
                        <label for=\"brand\">$row->brand</label>
                      </div>";

                $num++;
            }
        }
    }
}
