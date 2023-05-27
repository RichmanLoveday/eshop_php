<?php

use app\core\Models;
use app\core\Database;

class Settings
{
    protected static $SETTINGS = null;

    public function get_all(): array
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM settings";
        return $DB->read($query);
    }

    public function save(array  $post)
    {

        $DB = Database::newInstance();
        // loop through and submit post with data
        foreach ($post as $key => $value) {
            // if ($value === '') continue;
            $arr = [];
            $arr['setting'] = $key;


            if (strstr($key, '_link')) {
                if (trim($value) != "" && !strstr($value, 'https://')) {
                    $value = 'https://' . $value;
                }
            }

            $arr['value'] = $value;

            $query = "UPDATE settings SET value = :value WHERE setting = :setting limit 1";
            $DB->write($query, $arr);
        }
    }


    public static function __callStatic($name, $param)
    {
        if (self::$SETTINGS) {
            $settings = self::$SETTINGS;
        } else {
            $settings = self::get_all_setting_as_object();
        }

        return $settings->$name;
    }


    public static function get_all_setting_as_object(): object
    {

        $DB = Database::newInstance();
        $query = "SELECT * FROM settings";
        $data = $DB->read($query);


        $settings = (object)[];
        if (is_array($data)) {
            foreach ($data as $row) {
                $setting_name = $row->setting;
                $settings->$setting_name = $row->value;
            }
        }

        self::$SETTINGS = $settings;

        return $settings;
    }
}