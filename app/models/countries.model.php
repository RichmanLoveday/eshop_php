<?php

use app\core\Database;
use app\core\Models;

Class Countries extends Models {
    public function get_countries() {
        $DB = Database::newInstance();

        $query = "SELECT * FROM countries ORDER BY id DESC";
        $countries = $DB->read($query);

        return $countries;
    }


    public function get_states($id) {
        
        $arr['id'] = (int) $id;
        $DB = Database::newInstance();

        $query = "SELECT * FROM state WHERE parent = :id ORDER BY parent";
        $states = $DB->read($query, $arr);
        //show($states); die;
        return $states;

    }

    public function get_state($id) {
        
        $arr['id'] = (int) $id;
        $DB = Database::newInstance();

        $query = "SELECT state FROM state WHERE id = :id ORDER BY parent";
        $state = $DB->read($query, $arr);
       
        return is_array($state) ? $state[0] : false;

    }

    public function get_country($id) {
        $arr['id'] = (int) $id;
        $DB = Database::newInstance();

        $query = "SELECT country FROM countries WHERE id = :id";
        $country = $DB->read($query, $arr);

        return (is_array($country)) ? $country[0] : false;
    }


    public function make_countries($countries) {
        if(is_array($countries)) {
            $result = '<option>-- Select Country --</option>';

            // loop through
            foreach($countries as $country) {
                $result .= 
                    '
                        <option value="'.$country->id.'"class="country">'.$country->country.'</option>
                    ';
            }
            return $result;
        }
        return false;
    }

    
    public function make_state($states) {
        if(is_array($states)) {
            $result = '<option>-- State / Province / Region --</option>';

            // loop through
            foreach($states as $state) {
                $result .= 
                    '
                        <option value="'.$state->id.'"class="state">'.$state->state.'</option>
                    ';
            }
            return $result;
        }
        return false;
    }
}

?>