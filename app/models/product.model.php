<?php
use app\core\Database;
use app\core\Models;
use app\models\Auth;

Class Product extends Models {
    
    public function create($DATA, $FILES) {
        //show($DATA); die;
        $DB = Database::getInstance();
        
        $arr['description'] = ucwords($DATA->description);
        $arr['quantity'] = ucwords($DATA->quantity);
        $arr['category'] = ucwords($DATA->category);
        $arr['price'] = ucwords($DATA->price);
        $arr['date'] = date("Y-m-d H:i:s");
        $arr['user_url'] = $_SESSION['USER']->url_address;

        $error = false;
        // check if theirs an error in input
        if(!preg_match("/^[a-zA-Z ]+$/", trim($arr['description']))) {
            $this->errors['errorDescription'] = 'Please input a description name';
            $error = true;
        } 

        if(!is_numeric($arr['quantity'])) {
            $this->errors['errorQty'] = 'Please enter a valid quantity';
            $error = true;
        } 

        if(!is_numeric($arr['price'])) {
            $this->errors['errorPrice'] = 'Please enter a valid price';
            $error = true;
        } 

        if(!is_numeric($arr['category'])) {
            $this->errors['errorCat'] = 'Please enter a valid category';
            $error = true;
        } 

        // Check for files
        $arr["image"] = "";
        $arr["image2"] = "";
        $arr["image3"] = "";
        $arr["image4"] = "";
        
        // Rules for for 
        $allowed[] = "image/jpeg";
        $size = 10;
        $size = ($size * 1024 * 1024);
        
        $dir = "uploads/";
        
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }


        foreach($FILES as $key => $img_row) {
            if($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                if($img_row['size'] > $size) {
                    $this->errors['image_size'] = $key . " is bigger than the required size.";
                    return false;
                }

                $destination = $dir . $img_row['name'];
                move_uploaded_file($img_row['tmp_name'], $destination);
                $arr[$key] = $destination;
                
            } 
        }

        // If no error at all insert 
        if(!$error) {
            $query = "INSERT INTO products (description, price, quantity, category, date, user_url, image, image2, image3, image4) values (:description, :price, :quantity, :category, :date, :user_url, :image, :image2, :image3, :image4)";
            $check = $DB->write($query, $arr);

            // check if query ran
            if(!$check) {
                $this->errors['error'] = 'Unable to insert';
                return false;
            } 
            $this->success_message = 'Product added successfully';
            return true;
        }   
        
    }

    // Edit a category
    public function edit($DATA, $FILES) {
        $DB = Database::newInstance();


        $arr['id'] = (int) $DATA->id;
        $arr['description'] = ucwords($DATA->description);
        $arr['quantity'] = ucwords($DATA->quantity);
        $arr['price'] = ucwords($DATA->price);

        $error = false;
        // check if theirs an error in input
        if(!preg_match("/^[a-zA-Z ]+$/", trim($arr['description']))) {
            $this->errors['errorDescription'] = 'Please input a description name';
            $error = true;
        } 

        if(!is_numeric($arr['quantity'])) {
            $this->errors['errorQty'] = 'Please enter a valid quantity';
            $error = true;
        } 

        if(!is_numeric($arr['price'])) {
            $this->errors['errorPrice'] = 'Please enter a valid price';
            $error = true;
        } 
 

        // Validate inmages
        // Check for images images in data if set
        $arr["image"] = isset($DATA->image) ? $DATA->image : '';
        $arr["image2"] = isset($DATA->image2) ? $DATA->image2 : '';
        $arr["image3"] = isset($DATA->image3) ? $DATA->image3 : '';
        $arr["image4"] = isset($DATA->image4) ? $DATA->image4 : '';


        // Loop to check for new files uploaded
        // Rules for for 
        $allowed[] = "image/jpeg";
        $size = 10;
        $size = ($size * 1024 * 1024);
        
        $dir = "uploads/";
        
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }


        foreach($FILES as $key => $img_row) {
            if($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                if($img_row['size'] > $size) {
                    $this->errors['image_size'] = $key . " is bigger than the required size.";
                    return false;
                }

                $destination = $dir . $img_row['name'];
                move_uploaded_file($img_row['tmp_name'], $destination);
                $arr[$key] = $destination;
                
            } 
        }
        

        show($DATA);
        show($FILES);
        show($arr);
        die;
        $query = "UPDATE products SET description = :description WHERE id = :id limit 1";
        $check = $DB->write($query, $arr);

        if(!$check) return false;

        $this->success_message = "Your row was successfully edited";
        return true;
    }


    public function delete($id, $description) {
        
        $DB = Database::newInstance();
        $id = (int) $id;
        $query = "DELETE FROM products WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        if(!$check) return false;

        $this->success_message = "Category $description deleted successfully";
        return true;
        
    }



    public function make_table($product) {

        $url =  ROOT  . 'ajax_product';
        $result = '';
        
        if(is_array($product)) {
            //show($product); die;
            foreach($product as $product_row) {
                // Loop throgh to get rows"
                //$state = $product_row->disabled  ? "'Disabled'" : "'Enabled'";
                $id = $product_row->id;
                $one_cat = $this->get_one_data('categories', 'id', $product_row->category);
                //show($one_cat); die;
               // $current_state = $product_row->disabled ? 'label-warning' : 'label-info';
                $result .=
                '<tr>
                    <td><a href="#">'.$product_row->description.'</a></td>
                    <td><a href="#">'.$one_cat->category.'</a></td>
                    <td><a href="#">'.$product_row->quantity.'</a></td>
                    <td><a href="#">'.$product_row->price.'</a></td>
                    <td><a href="#">'.date("jS M, Y H:i:s", strtotime($product_row->date)).'</a></td>
                    <td><a href="#"><img src="'.ROOT.$product_row->image.'" style="width: 50px; height: 50px;"></a></td>
                    <td>
                        <button data-rowId="'.$id.'" data-rowUrl="'.$url.'" class="btn btn-primary btn-xs editProduct" style="outline: none;"><i class="fa fa-pencil" style="pointer-events:none;"></i></button>
                        <button data-rowId="'.$product_row->id.'" data-rowUrl="'.$url.'" data-productname="'.$product_row->description.'" class="btn btn-danger btn-xs deleteProduct" style="outline: none;"><i class="fa fa-trash-o " style="pointer-events:none;"></i></button>
                    </td>
                </tr>';
            }
        }
        return $result; 
    }
}


?>