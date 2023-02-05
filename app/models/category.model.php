<?php

use app\core\Database;
use app\core\Models;

Class Category extends Models {

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

        $arr = ['id' => $id, 'category' => $category];
        $query = "UPDATE categories SET category = :category WHERE id = :id limit 1";
        $check = $DB->write($query, $arr);

        if(!$check) return false;

        $this->success_message = "Your row was successfully edited";
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
        $disabled = ($state === "Enabled") ? 1 : 0;
        
        
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


    public function get_active_cat() {
        
        $DB = Database::newInstance();
        $query = "SELECT * FROM categories WHERE disabled = '0'";
        $category = $DB->read($query);

        if(empty($category)) return false;

        return $category;
    }
    

    public function make_table($cats) {

        $url =  ROOT  . 'ajax_category';
        $result = '';
        
        if(is_array($cats)) {
            foreach($cats as $cat_row) {
                // Loop throgh to get rows"
                $state = $cat_row->disabled  ? "Disabled" : "Enabled";
                $id = $cat_row->id;
                $current_state = $cat_row->disabled ? 'label-warning' : 'label-info';
                $result .=
                '<tr>
                    <td><a href="basic_table.html#">'.$cat_row->category.'</a></td>
                    <td><span data-rowId="'.$cat_row->id.'" data-rowUrl="'.$url.'" data-rowState="'.$state.'" class="label '.$current_state.' label-mini disable_row" style="cursor: pointer;">'. str_replace("'", '', $state) . '</span></td>
                    <td>
                        <button data-rowId="'.$id.'" data-rowUrl="'.$url.'" class="btn btn-primary btn-xs row_edit" style="outline: none;"><i class="fa fa-pencil" style="pointer-events:none;"></i></button>
                        <button data-rowId="'.$cat_row->id.'" data-rowUrl="'.$url.'" data-catname="'.$cat_row->category.'" data-state="'.$state.'" class="btn btn-danger btn-xs row_delete" style="outline: none;"><i class="fa fa-trash-o " style="pointer-events:none;"></i></button>
                    </td>
                </tr>';
            }
        }
        return $result; 
    }

}


?>