<?php

use app\core\Database;
use app\core\Models;
use app\models\Auth;

class Product extends Models
{

    public function create($DATA, $FILES, $image_class = null)
    {
        //show($DATA); die;
        $DB = Database::getInstance();

        $arr['description'] = ucwords($DATA->description);
        $arr['quantity'] = ucwords($DATA->quantity);
        $arr['category'] = ucwords($DATA->category);
        $arr['brand'] = (int) $DATA->brand;
        $arr['price'] = ucwords($DATA->price);
        $arr['date'] = date("Y-m-d H:i:s");
        $arr['user_url'] = $_SESSION['USER']->url_address;
        $arr['slag'] =  $this->str_to_url($DATA->description);

        $error = false;
        // check if theirs an error in input
        if (!preg_match("/^[a-zA-Z 0-9._\-,]+$/", trim($arr['description']))) {
            $this->errors['errorDescription'] = 'Please input a description name';
            $error = true;
        }

        if (!is_numeric($arr['quantity'])) {
            $this->errors['errorQty'] = 'Please enter a valid quantity';
            $error = true;
        }

        if (!is_numeric($arr['price'])) {
            $this->errors['errorPrice'] = 'Please enter a valid price';
            $error = true;
        }

        if (!is_numeric($arr['category'])) {
            $this->errors['errorCat'] = 'Please enter a valid category';
            $error = true;
        }


        // make sure slag is unique
        $query = "SELECT slag FROM products WHERE slag = :slag limit 1";
        $check = $DB->read($query, ['slag' => $arr['slag']]);

        // check if query ran
        if ($check) {
            $arr['slag'] .= '-' . rand(0, 99999);
        }

        // Check for files
        $arr["image"] = "";
        $arr["image2"] = "";
        $arr["image3"] = "";
        $arr["image4"] = "";

        // Rules for image uploads
        $allowed = ["image/png", "image/jpeg"];

        $size = 10;
        $size = ($size * 1024 * 1024);

        $dir = "uploads/";

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }


        foreach ($FILES as $key => $img_row) {
            // var_dump($key);
            if ($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                if ($img_row['size'] > $size) {
                    $this->errors['image_size'] = $key . " is bigger than the required size.";
                    return false;
                }

                $destination = $dir . $image_class->generate_filename(60) . ".jpg";
                move_uploaded_file($img_row['tmp_name'], $destination);
                $arr[$key] = $destination;

                $image_class->resize_image($destination, $destination, 1500, 1500, $img_row['type']);
            }
        }

        // If no error at all insert 
        if (!$error) {
            $query = "INSERT INTO products (description, price, quantity, category, brand, date, user_url, image, image2, image3, image4, slag) values (:description, :price, :quantity, :category, :brand, :date, :user_url, :image, :image2, :image3, :image4, :slag)";
            $check = $DB->write($query, $arr);

            // check if query ran
            if (!$check) {
                $this->errors['error'] = 'Unable to insert';
                return false;
            }
            $this->success_message = 'Product added successfully';
            return true;
        }
    }

    // Edit a category
    public function edit($DATA, $FILES, $image_class = null)
    {
        $DB = Database::newInstance();

        $arr['id'] = (int) $DATA->id;
        $arr['description'] = ucwords($DATA->description);
        $arr['quantity'] = ucwords($DATA->quantity);
        $arr['price'] = ucwords($DATA->price);
        $arr['category'] = $DATA->category;
        $arr['brand'] = (int) $DATA->brand;


        $error = false;
        // check if theirs an error in input
        if (!preg_match("/^[a-zA-Z ]+$/", trim($arr['description']))) {
            $this->errors['errorDescription'] = 'Please input a description name';
            $error = true;
        }

        if (!is_numeric($arr['quantity'])) {
            $this->errors['errorQty'] = 'Please enter a valid quantity';
            $error = true;
        }

        if (!is_numeric($arr['price'])) {
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
        $allowed = ["image/png", "image/jpeg"];
        $size = 10;
        $size = ($size * 1024 * 1024);

        $dir = "uploads/";

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }


        foreach ($FILES as $key => $img_row) {
            if ($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                if ($img_row['size'] > $size) {
                    $this->errors['image_size'] = $key . " is bigger than the required size.";
                    return false;
                }

                $destination = $dir . $image_class->generate_filename(60) . ".jpg";
                move_uploaded_file($img_row['tmp_name'], $destination);
                $arr[$key] = $destination;
                $image_class->resize_image($destination, $destination, 1500, 1500, $img_row['type']);
            }
        }

        $query = "UPDATE products SET description = :description, quantity = :quantity, category = :category, brand = :brand, price = :price, image = :image, image2 = :image2, image3 = :image3, image4 = :image4 WHERE id = :id limit 1";
        $check = $DB->write($query, $arr);

        if (!$check) return false;

        $this->success_message = "Product successfully edited";
        return true;
    }


    public function delete($id)
    {

        $DB = Database::newInstance();
        $id = (int) $id;
        $query = "DELETE FROM products WHERE id = '$id' limit 1";
        $check = $DB->write($query);

        if (!$check) return false;

        $this->success_message = "Product deleted successfully";
        return true;
    }


    public function featured_items($find = null, $limit, $offset)
    {
        $db = Database::newInstance();
        $rows = '';
        if ($find) {
            $query = "SELECT * FROM products WHERE description like :description limit $limit offset $offset";
            $rows = $db->read($query, ['description' => '%' . $find . '%']);
        } else {
            $query = "SELECT * FROM products limit $limit offset $offset";
            $rows = $db->read($query);
        }


        if (!$rows) return false;

        return $rows;
    }

    public function get_all_products($limit, $offset, $search = null)
    {
        $db = Database::newInstance();
        $query = "SELECT prod.*, cat.category as cat_name, brands.brand as brand_name FROM products as prod join brands on brands.id = prod.brand join categories as cat on cat.id = prod.category order by prod.id = prod.category desc limit $limit offset $offset";

        $result = $db->read($query);

        if (!$result) return false;

        return $result;
    }


    public function get_product_by_search(array $search, $limit, $offset)
    {
        $db = Database::newInstance();
        $query = '';
        $query .= "SELECT prod.*, cat.category as cat_name, brands.brand as brand_name FROM products as prod join brands on brands.id = prod.brand join categories as cat on cat.id = prod.category ";


        if (count($search) > 0) {
            $query .= " where ";
        }

        if (isset($search['description'])) {
            $query .= " prod.description like '%$search[description]%' and ";
        }

        if (isset($search['category']) && $search['category'] != '---Any category---') {
            $query .= " prod.category like '%$search[category]%' and ";
        }

        if (isset($search['year']) && $search['year'] != '---Select year---') {
            $query .= " Year(prod.date) = '$search[year]' and ";
        }

        if (isset($search['min_price']) && isset($search['max_price'])) {
            $query .= " prod.price between $search[min_price] and $search[max_price] and ";
        }

        if (isset($search['min_qty']) && isset($search['max_qty'])) {
            $query .= " prod.price between $search[min_qty] and $search[max_qty] and ";
        }

        // for brand 
        if (isset($search['brands']) && is_array($search['brands'])) {
            $query .= " brands.id in (" . trim(implode(',', $search['brands']), ',') . ") and ";
        }


        $query = trim($query);
        $query = trim($query, 'and');


        $query .= " order by prod.id = prod.category desc limit $limit offset $offset ";


        $result = $db->read($query);

        if (!$result) return false;

        return $result;
    }

    public function get_products_by_cat_id($cat_id, $type = null)
    {
        $db = Database::newInstance();

        if ($type == 'segment') {
            $query = "SELECT * FROM products WHERE category = :cat_id order by rand() limit 3";
        } else {
            $query = "SELECT * FROM products WHERE category = :cat_id ";
        }
        $rows = $db->read($query, ['cat_id' => $cat_id]);

        if (empty($rows)) return false;

        return $rows;
    }

    public function single_product($slag)
    {
        $db = Database::newInstance();
        $query = "SELECT * FROM products WHERE slag = :slag limit 1";
        $row = $db->read($query, ['slag' => $slag]);

        if (!$row) return false;
        // show($row); die;
        if (is_array($row)) {
            return $row[0];
        }
    }

    public function get_random_product()
    {
        $db = Database::newInstance();
        $query = "SELECT * FROM products WHERE rand() limit 3 ";
        $result = $db->read($query);

        if (!$result) return false;

        return $result;
    }


    public function make_table($product)
    {
        $url =  ROOT  . 'ajax_product';
        $result = '';

        if (is_array($product)) {
            //show($product); die;
            foreach ($product as $product_row) {
                // Loop throgh to get rows"
                //$state = $product_row->disabled  ? "'Disabled'" : "'Enabled'";
                // $one_cat = $this->get_one_data('categories', 'id', $product_row->category);
                //show($one_cat); die;
                // $current_state = $product_row->disabled ? 'label-warning' : 'label-info';

                $id = $product_row->id;
                $result .=
                    '<tr>
                    <td><a href="#">' . $product_row->description . '</a></td>
                    <td><a href="#">' . $product_row->cat_name . '</a></td>
                    <td><a href="#">' . $product_row->brand_name . '</a></td>
                    <td><a href="#">' . $product_row->quantity . '</a></td>
                    <td><a href="#">' . $product_row->price . '</a></td>
                    <td><a href="#">' . date("jS M, Y H:i:s", strtotime($product_row->date)) . '</a></td>
                    <td><a href="#"><img src="' . ROOT . $product_row->image . '" style="width: 50px; height: 50px;"></a></td>
                    <td>
                        <button data-rowId="' . $id . '" data-rowUrl="' . $url . '" class="btn btn-primary btn-xs editProduct" style="outline: none;"><i class="fa fa-pencil" style="pointer-events:none;"></i></button>
                        <button data-rowId="' . $product_row->id . '" data-rowUrl="' . $url . '" data-page_num="' . $product_row->page_num . '" data-productname="' . $product_row->description . '" class="btn btn-danger btn-xs deleteProduct" style="outline: none;"><i class="fa fa-trash-o " style="pointer-events:none;"></i></button>
                    </td>
                </tr>';
            }
        }
        return $result;
    }


    public function str_to_url($url)
    {
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }
}
