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


    public static function advance_search(array $GET)
    {
        // loop to check for needed datas
        $search_datas = [];
        if (isset($GET['description']) && trim(htmlspecialchars($GET['description'])) != '') {
            $search_datas['description'] = $GET['description'];
        }

        if (isset($GET['category']) && trim(htmlspecialchars($GET['category'])) != '') {
            $search_datas['category'] = $GET['category'];
        }

        if (isset($GET['min-price']) && $GET['min-price'] > 0) {
            $search_datas['min_price']  = (float) $GET['min-price'];
            $search_datas['max_price']  = (float) $GET['max-price'];
        }

        if (isset($GET['min-qty']) && $GET['min-qty'] > 0) {
            $search_datas['min_qty']  = (int) $GET['min-qty'];
            $search_datas['max_qty']  = (int) $GET['max-qty'];
        }

        // for brand
        foreach ($GET as $key => $search_row) {
            if (strstr($key, 'brand-')) {
                $search_datas['brands'][$key] = $search_row;
            }
        }

        return $search_datas;
    }
}
