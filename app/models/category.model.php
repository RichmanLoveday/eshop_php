<?php

use app\core\Database;

Class Category{
    public array $errors = [];
    public string $success_message;

    public function create($DATA) {

        $DB = Database::getInstance();

        $arr['category'] = ucwords($DATA->data);

        // check if theirs an error in input
        if(!preg_match("/^[a-zA-Z ]+$/", trim($arr['category']))) {
            $this->errors['error'] = 'Please input a correct category name';
            return false;
        } 

        $query = "INSERT INTO categories (category) values (:category)";
        $check = $DB->write($query, $arr);

        // check if query ran
        if(!$check) {
            $this->errors['error'] = 'Unable to update category';
            return false;
        } 
        $this->success_message = 'Category added successfully';
        return true;
    }

    public function edit($id, $category) {
        $DB = Database::newInstance();
        $id = (int) $id;

        // echo $id;
        // echo $category;
        // die;
        $query = "UPDATE categories SET category = '$category' WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        if(!$check) return false;

        $this->success_message = "Category $category successfully edited";
        return true;
    }

    public function delete($id, $category) {
        
        $DB = Database::newInstance();
        $id = (int) $id;
        $query = "DELETE FROM categories WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        if(!$check) return false;

        $this->success_message = "Category $category deleted successfully";
        return true;
        
    }

    public function disable_row($id, $state) {

        // Check state
        $disabled = ($state === 'Enabled') ? 1 : 0;
        
        // Update database
        $DB = Database::newInstance();
        $query = "UPDATE categories SET disabled = '$disabled' WHERE id = '$id' limit 1";
        $check = $DB->write($query);
        

        // check if query ran
        if(!$check) return false;

        $state_ = str_replace("'", '', $state);
        // echo $state_;
        $this->success_message = "Category '$state_' successfully";
        return true;
    }


    public function get_all_data() {
        $DB = Database::newInstance();
        $query = "SELECT * FROM categories ORDER BY id DESC";
        $data = $DB->read($query);

        if(!$data) return false;

        return $data;
        
    }


    public function get_single_data($id) {
        $DB = Database::newInstance();
        $query = "SELECT * FROM categories WHERE id = :id limit 1";
        $data = $DB->read($query, ['id' => $id]);

        if(!$data) return false;

        return $data[0];
    }


    public function make_table($cats) {

        $url = "'". ROOT  . 'ajax' . "'";
        $result = '';
        
        if(is_array($cats)) {
            foreach($cats as $cat_row) {
                // Loop throgh to get rows"
                $state = $cat_row->disabled  ? "'Disabled'" : "'Enabled'";
                $id = $cat_row->id;
                $current_state = $cat_row->disabled ? 'label-warning' : 'label-info';
                $result .=
                '<tr>
                    <td><a href="basic_table.html#">'.$cat_row->category.'</a></td>
                    <td><span data-rowId="'.$cat_row->id.'" class="label '.$current_state.' label-mini" onclick="disable_row('.$url.', '.$state.', event)" style="cursor: pointer;">'. str_replace("'", '', $state) . '</span></td>
                    <td>
                        <button data-rowId="'.$id.'" onclick="edit_row('.$url.','.$id.', event)" class="btn btn-primary btn-xs row_edit" style="outline: none;"><i class="fa fa-pencil" style="pointer-events:none;"></i></button>
                        <button data-rowId="'.$cat_row->id.'" onclick="delete_row('.$url.', '.$state.', event)" class="btn btn-danger btn-xs row_delete" style="outline: none;"><i class="fa fa-trash-o " style="pointer-events:none;"></i></button>
                    </td>
                </tr>';
            }
        }
        return $result; 
    }
}


?>