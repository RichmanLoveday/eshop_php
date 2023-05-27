<?php

use app\core\Database;
use app\core\Models;

class Countries extends Models
{
    public function get_countries()
    {
        $DB = Database::newInstance();

        $query = "SELECT * FROM countries ORDER BY id DESC";
        $countries = $DB->read($query);

        return $countries;
    }


    public function get_states($country)
    {


        $arr['country'] = addslashes($country);
        $DB = Database::newInstance();

        $query = "SELECT * FROM countries WHERE country = :country limit 1";
        $check = $DB->read($query, $arr);
        $state = false;

        if (is_array($check)) {
            $arr = [];
            $arr['id'] = $check[0]->id;
            $query = "SELECT * FROM states WHERE parent = :id ORDER BY parent";
            $state = $DB->read($query, $arr);
        }

        return is_array($state) ? $state : false;
    }

    public function get_state($id)
    {

        $arr['id'] = (int) $id;
        $DB = Database::newInstance();

        $query = "SELECT state FROM states WHERE id = :id ORDER BY parent";
        $state = $DB->read($query, $arr);

        return is_array($state) ? $state[0] : false;
    }

    public function get_country($id)
    {
        $arr['id'] = (int) $id;
        $DB = Database::newInstance();

        $query = "SELECT country FROM countries WHERE id = :id";
        $country = $DB->read($query, $arr);

        return (is_array($country)) ? $country[0] : false;
    }


    public function make_countries($countries)
    {
        if (is_array($countries)) {
            $result = '';

            // loop through
            foreach ($countries as $country) {
                $result .=
                    '
                        <option value="' . $country->country . '"class="country">' . $country->country . '</option>
                    ';
            }
            return $result;
        }
        return false;
    }


    public function make_state($states)
    {
        if (is_array($states)) {
            $result = '';

            // loop through
            foreach ($states as $state) {
                $result .=
                    '
                        <option value="' . $state->state . '"class="state">' . $state->state . '</option>
                    ';
            }
            return $result;
        }
        return false;
    }
}
