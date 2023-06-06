<?php

use app\core\Database;
use app\core\Models;

class Message extends Models
{

    public function create($DATA)
    {

        $DB = Database::getInstance();

        $arr['name'] =  ucwords($DATA->name);
        $arr['email'] =   $DATA->email;
        $arr['subject'] =    ucwords($DATA->subject);
        $arr['message'] =    trim($DATA->message);
        $arr['date'] =  date('Y-m-d H:i:s');


        // check if theirs an error in input
        if (!preg_match("/^[a-zA-Z ]+$/", trim($arr['name']))) {
            $this->errors['errName'] = 'Please enter a valid name';
        }

        if (empty(trim($arr['subject']))) {
            $this->errors['errSubj'] = 'Only letters and spaces allowed in subject';
        }

        if (!filter_var($arr['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['errEmail'] = 'Please enter a valid email';
        }

        if (empty($arr['message'])) {
            $this->errors['errMssg'] = 'Please enter a valid message';
        }


        if (count($this->errors) == 0) {
            $query = "INSERT INTO contact_us (name, subject, message, email,date) values (:name, :subject, :message, :email, :date)";
            $check = $DB->write($query, $arr);

            // check if query ran
            if (!$check) {
                $this->errors['error'] = 'Unable to update contact';
                return false;
            }
            $this->success_message = 'Contact added successfully';

            return true;
        }

        return false;
    }

    public function edit($data)
    {
        //show($data); die;
        $DB = Database::newInstance();
        $id = (int) $data->id;

        $arr = ['id' => $id, 'category' => $data->category_edit, 'parent' => $data->parent_edit];
        $query = "UPDATE categories SET category = :category, parent = :parent WHERE id = :id limit 1";
        $check = $DB->write($query, $arr);

        if (!$check) return false;

        $this->success_message = "Your row was successfully edited";
        return true;
    }

    public function delete($id, $category)
    {

        $DB = Database::newInstance();
        $id = (int) $id;
        $query = "DELETE FROM contact_us WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        if (!$check) return false;

        $this->success_message = "Contact deleted successfully";
        return true;
    }

    public function disable_row($id, $state)
    {
        // Check state
        $disabled = ($state === "Enabled") ? 1 : 0;


        // Update database
        $DB = Database::newInstance();
        $query = "UPDATE categories SET disabled = '$disabled' WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        // check if query ran
        if (!$check) return false;

        $state_ = str_replace("'", '', $state);
        // echo $state_;
        $this->success_message = "Category '$state_' successfully";
        return true;
    }

    public function get_all()
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM contact_us";
        return $DB->read($query);
    }

    public function delete_message($id)
    {
        $id = (int) $id;
        $DB = Database::newInstance();
        $query = "DELETE FROM contact_us WHERE id = '$id' limit 1";
        $result = $DB->write($query);

        if (!$result) return false;

        $this->success_message = "The message was deleted successfully";
        return true;
    }
}
