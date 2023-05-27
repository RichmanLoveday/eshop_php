<?php

namespace app\core;

use app\core\Database;

class Models
{
    public array $errors = [];
    public string $success_message;

    // FETCH single row
    public function get_single_data(string $table, $id)
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM $table WHERE id = :id limit 1";
        $data = $DB->read($query, ['id' => $id]);

        if (!$data) return false;

        return $data[0];
    }

    // GET all data
    public function get_all_data(string $table)
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM $table ORDER BY id DESC";
        $data = $DB->read($query);

        if (!$data) return false;

        return $data;
    }

    public function get_one_data(string $table, string $rowId,  $id)
    {
        $id = (int) $id;
        $DB = Database::newInstance();
        $query = "SELECT * FROM $table WHERE $rowId = '$id' limit 1";
        $data = $DB->read($query);

        if (!$data) return false;

        return $data[0];
    }
}
