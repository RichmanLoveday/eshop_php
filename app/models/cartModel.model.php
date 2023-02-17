<?php

use app\core\Database;
use app\core\Models;

Class CartModel extends Models {
    
    public function add_to_cart(string $cart_index, object $data) {
        //show($data);
        if(isset($_SESSION[$cart_index])) {
            $ids = array_column($_SESSION[$cart_index], 'id');

            // check if id exist in ids else add new data
            if(in_array($data->id, $ids)) {
                $key = array_search($data->id, $ids);       // search for key of id
                //show($key);
                $_SESSION[$cart_index][$key]->qty++;      // increment qty in session key
                
            } else {
                $_SESSION[$cart_index][] = $data;
            }
        
        } else {
            $_SESSION[$cart_index][] = $data;
        }
        $this->success_message = 'Product added successfully';
        //show($_SESSION);

        return true;
   
    }


    public function get_products($ids) {
        $DB = Database::newInstance();
        $query = "SELECT * FROM products WHERE id in ($ids)";
        $rows = $DB->read($query);
    
        if(!$rows) return false;

        return $rows;

    }


    public function make_table($products) {

        $result = '';
        if(is_array($products)) {
            foreach($products as $item) {
                $result .= '
                <tr>
                    <td class="cart_product">
                        <a href=""><img style="width:50px; height:50px;" src="'. ROOT . $item->image . '"
                                alt=""></a>
                    </td>
                    <td class="cart_description">
                        <h4><a href="">'. $item->description . '</a></h4>
                        <p>prod ID: '.$item->id.'</p>
                    </td>
                    <td class="cart_price">
                        <p>$'.$item->price.'</p>
                    </td>
                    <td class="cart_quantity">
                        <div class="cart_quantity_button">
                            <a data-url="'.ROOT. 'ajax_cart" data-id="'. $item->id .'" class="cart_quantity_down"
                        href=""> - </a>
                        <input class="cart_quantity_input" value="'.$item->cart_qty.'" type="text" name="quantity" value="1" autocomplete="off"
                            size="2">
                        <a data-url="'.ROOT. 'ajax_cart" data-id="'.$item->id.'" class="cart_quantity_up" href=""> + </a>

                        </div>
                        </td>
                        <td class="cart_total">
                            <p class="cart_total_price">$'.$item->price * $item->cart_qty.'</p>
                        </td>
                        <td class="cart_delete">
                            <a data-url="'.ROOT. 'ajax_cart" data-id="'.$item->id.'" class="cart_quantity_delete" href=""><i class="fa fa-times"></i></a>
                        </td>
                    </td>
                </tr>
                
                ';
            }
        } else {
            $result .= '
                <div>Cart is empty</div>
            ';
        }
        return $result;

    }

    public function increase_quantity(string $cart_index, string $id, $image_class) {
        if(isset($_SESSION[$cart_index])) {
            //show($_SESSION[$cart_index]);
            foreach($_SESSION[$cart_index] as $key => $item) {
                if($item->id === $id) {
                    $item->qty++;
                    break;
                }
            }
            //show($_SESSION[$cart_index]);
            
            // return to updated table
            //show($_SESSION[$cart_index]);
            $ids_str = array_column($_SESSION[$cart_index], 'id');
            $prod_ids = "'". implode("','", $ids_str) . "'";
            $products = $this->get_products($prod_ids);

            // create cart quantity and reduce image
            if(is_array($products)) {
                foreach($products as $key => $row) {
                    // resize image
                    $products[$key]->image = $image_class->get_thumb_post($products[$key]->image);
                    
                    // loop through session carts
                    foreach($_SESSION[$cart_index] as $item) {
                       if($row->id == $item->id) {
                        $products[$key]->cart_qty = $item->qty;
                        break;
                       }
                    }
                
                }
            }

            return $this->make_table($products);        // updated table
        }
        return false;
    }

    public function decrease_quantity(string $cart_index, string $id, $image_class) {
        if(isset($_SESSION[$cart_index])) {
            //show($_SESSION[$cart_index]);
            foreach($_SESSION[$cart_index] as $key => $item) {
                if($item->id === $id) {
                    $item->qty--;
                    break;
                }
            }
            //show($_SESSION[$cart_index]);

            // return to updated table
           // show($_SESSION[$cart_index]);
            $ids_str = array_column($_SESSION[$cart_index], 'id');
            $prod_ids = "'". implode("','", $ids_str) . "'";
            $products = $this->get_products($prod_ids);

            // create cart quantity and reduce image
            if(is_array($products)) {
                foreach($products as $key => $row) {
                    // resize image
                    $products[$key]->image = $image_class->get_thumb_post($products[$key]->image);
                    
                    // loop through session carts
                    foreach($_SESSION[$cart_index] as $item) {
                       if($row->id == $item->id) {
                        $products[$key]->cart_qty = $item->qty;
                        break;
                       }
                    }
                
                }
            }

            return $this->make_table($products);        // updated table
        }
        return false;
    }

    public function remove_cart(string $cart_index, string $id, $image_class) {
        if(isset($_SESSION[$cart_index])) {
            show($_SESSION[$cart_index]);
            foreach($_SESSION[$cart_index] as $key => $item) {
                if($item->id === $id) {
                    unset($_SESSION[$cart_index][$key]);
                    $_SESSION[$cart_index] = array_values($_SESSION[$cart_index]);
                    break;
                }
            }

            // return to updated table
            show($_SESSION[$cart_index]);
            $ids_str = array_column($_SESSION[$cart_index], 'id');
            $prod_ids = "'". implode("','", $ids_str) . "'";
            $products = $this->get_products($prod_ids);

            // create cart quantity and reduce image
            if(is_array($products)) {
                foreach($products as $key => $row) {
                    // resize image
                    $products[$key]->image = $image_class->get_thumb_post($products[$key]->image);
                    
                    // loop through session carts
                    foreach($_SESSION[$cart_index] as $item) {
                       if($row->id == $item->id) {
                        $products[$key]->cart_qty = $item->qty;
                        break;
                       }
                    }
                
                }
            }

            return $this->make_table($products);        // updated table
        }
        return false;
    }
}

?>