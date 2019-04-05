<?php
/**
 * product_admin.php
 *
 * Product management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestProductAdmin extends Controller {

    private static $defaultFields = array(
        "model",
        "sku",
        "upc",
        "ean",
        "jan",
        "isbn",
        "mpn",
        "location",
        "quantity",
        "minimum",
        "subtract",
        "stock_status_id",
        "date_available",
        "manufacturer_id",
        "shipping",
        "price",
        "points",
        "weight",
        "weight_class_id",
        "length",
        "width",
        "height",
        "length_class_id",
        "status",
        "tax_class_id",
        "sort_order",
        "image",
        "product_store"
    );

    private static $defaultFieldValues = array(
        "quantity"=>1,
        "minimum"=>1,
        "subtract"=>1,
        "stock_status_id"=>0,
        "shipping"=> 1,
        "manufacturer_id"=> 0,
        "status"=>1,
        "product_store"=>array(0),
        "tax_class_id"=> 0,
        "sort_order" => 1
    );


    /*
    * PRODUCT FUNCTIONS
    * index.php?route=rest/product_admin/products
    */
    public function products() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get product details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getProduct($this->request->get['id']);
            }else {
                //get products list

                /*check category id parameter*/
                if (isset($this->request->get['category']) && ctype_digit($this->request->get['category'])) {
                    $category_id = $this->request->get['category'];
                } else {
                    $category_id = 0;
                }

                $this->listProducts($category_id, $this->request);
            }
        }else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //insert product
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addProduct($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            //update product
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateProduct($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteProduct($this->request->get['id']);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    /*
    * Get products list
    */
    public function listProducts($category_id, $request) {

        $json = array('success' => false);

        $this->load->model('rest/restadmin');

        $parameters = array(
            "limit" => 100,
            "start" => 1,
            'filter_category_id' => $category_id
        );

        /*check limit parameter*/
        if (isset($request->get['limit']) && ctype_digit($request->get['limit'])) {
            $parameters["limit"] = $request->get['limit'];
        }

        /*check page parameter*/
        if (isset($request->get['page']) && ctype_digit($request->get['page'])) {
            $parameters["start"] = $request->get['page'];
        }

        /*check search parameter*/
        if (isset($request->get['search']) && !empty($request->get['search'])) {
            $parameters["filter_name"] = $request->get['search'];
            $parameters["filter_tag"]  = $request->get['search'];
        }


        /*check sort parameter*/
        if (isset($request->get['sort']) && !empty($request->get['sort'])) {
            $parameters["sort"] = $request->get['sort'];
        }

        /*check order parameter*/
        if (isset($request->get['order']) && !empty($request->get['order'])) {
            $parameters["order"] = $request->get['order'];
        }

        $parameters["start"] = ($parameters["start"] - 1) * $parameters["limit"];

        $products = $this->model_rest_restadmin->getProductsData($parameters, $this->customer);

        if (count($products) == 0 || empty($products)) {
            $json['success'] = false;
            $json['error'] = "No product found";
        } else {
            $json['success'] = true;
            foreach ($products as $product) {
                $json['data'][] = $this->getProductInfo($product);
            }
        }

        $this->sendResponse($json);
    }

    /*
    * Get product details
    */
    public function getProduct($id) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');

        $products = $this->model_rest_restadmin->getProductsByIds(array($id), $this->customer);
        if(!empty($products)) {
            $json["data"] = $this->getProductInfo(reset($products));
        } else {
            $json['success']     = false;
        }

        $this->sendResponse($json);
    }

    private function getProductInfo($product){

        $this->load->model('tool/image');
        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        //product image
        if (isset($product['image']) && file_exists(DIR_IMAGE . $product['image'])) {
            $image = $this->model_tool_image->resize($product['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
        } else {
            $image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
        }

        //additional images
        $additional_images = $this->model_catalog_product->getProductImages($product['product_id']);

        $images = array();

        foreach ($additional_images as $additional_image) {
            if (isset($additional_image['image']) && file_exists(DIR_IMAGE . $additional_image['image'])) {
                $images[] = $this->model_tool_image->resize($additional_image['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
            } else {
                $images[] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
            }
        }

        //special
        $product_specials = $this->model_rest_restadmin->getProductSpecials($product['product_id']);

        $specials = array();

        foreach ($product_specials as $product_special) {
            $specials[] = array(
                'customer_group_id' => $product_special['customer_group_id'],
                'priority'          => $product_special['priority'],
                'price'             => $product_special['price'],
                'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
                'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
            );
        }

        //discounts
        $product_discounts = $this->model_rest_restadmin->getProductDiscounts($product['product_id']);

        $discounts = array();

        foreach ($product_discounts as $product_discount) {
            $discounts[] = array(
                'customer_group_id' => $product_discount['customer_group_id'],
                'quantity'          => $product_discount['quantity'],
                'priority'          => $product_discount['priority'],
                'price'             => $product_discount['price'],
                'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
                'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
            );
        }

        //options
        $options = array();

        foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();
                if(!empty($option['product_option_value'])){
                    foreach ($option['product_option_value'] as $option_value) {
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            if ((($this->customer->isLogged() && $this->config->get('config_customer_price')) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                                $price = $this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax'));
                                $price_formated = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
                            } else {
                                $price = false;
                                $price_formated = false;
                            }

                            if (isset($option_value['image']) && file_exists(DIR_IMAGE . $option_value['image'])) {
                                $option_image = $this->model_tool_image->resize($option_value['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                            } else {
                                $option_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                            }

                            $option_value_data[] = array(
                                'image'					=> $option_image,
                                'price'					=> $price,
                                'price_formated'		=> $price_formated,
                                'price_prefix'			=> $option_value['price_prefix'],
                                'product_option_value_id'=> $option_value['product_option_value_id'],
                                'option_value_id'		=> $option_value['option_value_id'],
                                'name'					=> $option_value['name'],
                                'quantity'	=> !empty($option_value['quantity']) ? $option_value['quantity'] : 0
                            );
                        }
                    }
                }
                $options[] = array(
                    'name'				=> $option['name'],
                    'type'				=> $option['type'],
                    'option_value'		=> $option_value_data,
                    'required'			=> $option['required'],
                    'product_option_id' => $option['product_option_id'],
                    'option_id'			=> $option['option_id'],

                );

            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $option_value  = array();
                if(!empty($option['product_option_value'])){
                    $option_value = $option['product_option_value'];
                }
                $options[] = array(
                    'name'				=> $option['name'],
                    'type'				=> $option['type'],
                    'option_value'		=> $option_value,
                    'required'			=> $option['required'],
                    'product_option_id' => $option['product_option_id'],
                    'option_id'			=> $option['option_id'],
                );
            }
        }


        $productCategories = array();
        $product_category  = $this->model_rest_restadmin->getProductCategories($product['product_id']);

        foreach ($product_category as $category) {
            $languageId = isset($category['language_id']) ? $category['language_id'] : (int)$this->config->get('config_language_id');
            $productCategories[$category['category_id']][] = array(
                'category_id'      => $category['category_id'],
                'name'             => $category['name'],
                'description'      => $category['description'],
                'sort_order'       => $category['sort_order'],
                'meta_title'       => $category['meta_title'],
                'meta_description' => $category['meta_description'],
                'meta_keyword'     => $category['meta_keyword'],
                'language_id'      => $languageId
            );

        }

        /*reviews*/
        $this->load->model('catalog/review');

        $reviews = array();

        $reviews["review_total"] = $this->model_catalog_review->getTotalReviewsByProductId($product['product_id']);

        $reviewList = $this->model_catalog_review->getReviewsByProductId($product['product_id'], 0, 1000);

        foreach ($reviewList as $review) {
            $reviews['reviews'][] = array(
                'author'     => $review['author'],
                'text'       => nl2br($review['text']),
                'rating'     => (int)$review['rating'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($review['date_added']))
            );
        }

        $product_attributes = $this->model_rest_restadmin->getProductAttributes($product['product_id']);

        return array(
            'id'				=> $product['product_id'],
            'seo_h1'			=> (!empty($product['seo_h1']) ? $product['seo_h1'] : "") ,
            'manufacturer'		=> $product['manufacturer'],
            'sku'				=> (!empty($product['sku']) ? $product['sku'] : "") ,
            'model'				=> $product['model'],
            'image'				=> $image,
            'images'			=> $images,
            'price'				=> $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')),
            'price_formated'    => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
            'rating'			=> (int)$product['rating'],
            'product_description'=> $product['product_description'],
            'product_attributes' => $product_attributes,
            'special'			=> $specials,
            'discounts'			=> $discounts,
            'options'			=> $options,
            'minimum'			=> $product['minimum'] ? $product['minimum'] : 1,
            'upc'              => $product['upc'],
            'ean'              => $product['ean'],
            'jan'              => $product['jan'],
            'isbn'             => $product['isbn'],
            'mpn'              => $product['mpn'],
            'location'         => $product['location'],
            'stock_status'     => $product['stock_status'],
            'manufacturer_id'  => $product['manufacturer_id'],
            'tax_class_id'     => $product['tax_class_id'],
            'date_available'   => $product['date_available'],
            'weight'           => $product['weight'],
            'weight_class_id'  => $product['weight_class_id'],
            'length'           => $product['length'],
            'width'            => $product['width'],
            'height'           => $product['height'],
            'length_class_id'  => $product['length_class_id'],
            'subtract'         => $product['subtract'],
            'sort_order'       => $product['sort_order'],
            'status'           => $product['status'],
            'date_added'       => $product['date_added'],
            'date_modified'    => $product['date_modified'],
            'viewed'           => $product['viewed'],
            'weight_class'     => $product['weight_class'],
            'length_class'     => $product['length_class'],
            'reward'			=> $product['reward'],
            'points'			=> $product['points'],
            'category'			=> $productCategories,
            'quantity'			=> !empty($product['quantity']) ? $product['quantity'] : 0,
            'currency_id'       => $this->currency->getId(),
            'currency_code'     => $this->currency->getCode(),
            'currency_value'    => $this->currency->getValue($this->currency->getCode()),
            'reviews' => $reviews
        );
    }

    /*	Update product

    */
    private function updateProduct($id, $data) {

        $json = array('success' => false);

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        if (ctype_digit($id)) {
            $valid = $this->model_rest_restadmin->checkProductExists($id);

            if(!empty($valid)) {
                $product = $this->model_catalog_product->getProduct($id);
                $this->loadProductSavedData($data, $product);
                if ($this->validateProductForm($data)) {
                    $json['success']     = true;
                    $this->model_rest_restadmin->editProductById($id, $data);
                } else {
                    $json['error']       = "Validation failed";
                    $json['success']     = false;
                }
            }else {
                $json['success']     = false;
                $json['error']       = "The specified product does not exist.";
            }
        }else {
            $json['success']     = false;
            $json['error']       = "Invalid identifier.";
        }

        $this->sendResponse($json);
    }

    /*
	Insert product
    */
    public function addProduct($data) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');

        if ($this->validateProductForm($data, true)) {
            $productId = $this->model_rest_restadmin->addProduct($data);
            $json['product_id'] = $productId;
        } else {
            $json['success']	= false;
        }

        $this->sendResponse($json);
    }

    /*
    * Delete product
    */
    public function deleteProduct($id) {

        $json['success']     = false;

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        if (ctype_digit($id)) {

            $product = $this->model_rest_restadmin->checkProductExists($id);

            if(!empty($product)) {
                $json['success']     = true;
                $this->model_rest_restadmin->deleteProduct($id);
            }else {
                $json['success']     = false;
                $json['error']       = "The specified product does not exist.";
            }
        }else {
            $json['success']     = false;
        }

        $this->sendResponse($json);
    }

    /*
    * BULK PRODUCT FUNCTIONS
    */
    public function bulkproducts() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //insert products
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {

                $this->addProducts($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            //update products
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {
                $this->updateProducts($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        }
    }

    /*
		Insert products
	*/
    public function addProducts($products) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');

        foreach($products as $product) {

            if ($this->validateProductForm($product, true)) {
                $this->model_rest_restadmin->addProduct($product);
            } else {
                $json['success']	= false;
            }
        }

        $this->sendResponse($json);
    }

    /*	Update products

    */
    private function updateProducts($products) {

        $json = array('success' => true);

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        foreach($products as $productItem) {

            $id = $productItem['product_id'];

            if (ctype_digit($id)) {

                $valid = $this->model_rest_restadmin->checkProductExists($id);

                if(!empty($valid)) {
                    $product = $this->model_catalog_product->getProduct($id);

                    $this->loadProductSavedData($productItem, $product);
                    if ($this->validateProductForm($productItem)) {
                        $this->model_rest_restadmin->editProductById($id, $productItem);
                    } else {
                        $json['success'] 	= false;
                    }

                } else {
                    $json['success']     = false;
                    $json['error']       = "The specified product does not exist.";
                }

            } else {
                $json['success']     = false;
                $json['error']       = "Invalid identifier";
            }
        }

        $this->sendResponse($json);
    }

    private function loadProductSavedData(&$data, $product) {
        foreach(self::$defaultFields as $field){
            if(!isset($data[$field])){
                if(isset($product[$field])){
                    $data[$field] = $product[$field];
                } else {
                    $data[$field] = "";
                }
            }
        }
    }

    private function validateProductForm(&$data, $validateSku = false) {

        $error = false;

        if($validateSku){
            if ((utf8_strlen($data['sku']) < 2) || (utf8_strlen($data['sku']) > 255)) {
                $error  = true;
            }
        }

        if (!empty($data['date_available'])) {
            $date_available = date('Y-m-d',strtotime($data['date_available']));
            if($this->validateDate($date_available, 'Y-m-d')) {
                $data['date_available'] = $date_available;
            } else{
                $data['date_available'] = date('Y-m-d');
            }
        }else{
            $data['date_available'] = date('Y-m-d');
        }

        if (!empty($data['length_class_id'])) {
            $data['length_class_id'] = $data['length_class_id'];
        }  else {
            $data['length_class_id'] = $this->config->get('config_length_class_id');
        }

        if (!empty($data['weight_class_id'])) {
            $data['weight_class_id'] = $data['weight_class_id'];
        }  else {
            $data['weight_class_id'] = $this->config->get('config_weight_class_id');
        }

        foreach(self::$defaultFields as $field){
            if(!isset($data[$field])){
                if(!isset(self::$defaultFieldValues[$field])){
                    $data[$field] = "";
                } else {
                    $data[$field] = self::$defaultFieldValues[$field];
                }
            }
        }

        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

    private function checkPlugin() {

        $this->config->set('config_error_display', 0);

        $this->response->addHeader('Content-Type: application/json');

        $json = array("success"=>false);

        /*check rest api is enabled*/
        /*check rest api is enabled*/
        /*if (!$this->config->get('restadmin_status')) {
            $json["error"] = 'Rest Admin API is disabled. Enable it!';
        }*/


        $headers = apache_request_headers();

        $key = "";

        if(isset($headers['X-Oc-Restadmin-Id'])){
            $key = $headers['X-Oc-Restadmin-Id'];
        }else if(isset($headers['X-OC-RESTADMIN-ID'])) {
            $key = $headers['X-OC-RESTADMIN-ID'];
        }

        /*validate api security key*/
        if ($this->config->get('restadmin_key') && ($key != $this->config->get('restadmin_key'))) {
            $json["error"] = 'Invalid secret key';
        }

        if(isset($json["error"])){
            echo(json_encode($json));
            exit;
        }else {
            $this->response->setOutput(json_encode($json));
        }
    }

    /*
    * FEATURED PRODUCTS FUNCTIONS
    */
    public function featured() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get featured products
            $limit = 0;

            if (isset($this->request->get['limit']) && ctype_digit($this->request->get['limit']) && $this->request->get['limit'] > 0) {
                $limit = $this->request->get['limit'];
            }

            $this->getFeaturedProducts($limit);
        }
    }

    /*
    * Get featured products
    */
    public function getFeaturedProducts($limit) {

        $json = array('success' => true);

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $products = explode(',', $this->config->get('featured_product'));

        if($limit){
            $products = array_slice($products, 0, (int)$limit);
        }

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                if ($product_info['image']) {
                    $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                } else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                    $price_formated = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                    $price_formated = false;
                }

                if ((float)$product_info['special']) {
                    $special = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                    $special_formated = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                    $special_formated = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }

                $json['data'][] = array(
                    'product_id' => $product_info['product_id'],
                    'thumb'   	 => $image,
                    'name'    	 => $product_info['name'],
                    'price'   	 => $price,
                    'price_formated'   	 => $price_formated,
                    'special' 	 => $special,
                    'special_formated' 	 => $special_formated,
                    'rating'     => $rating
                );
            }
        }

        if(!isset($json["data"])){
            $json["error"] = "No featured product found";
            $json["success"] = false;
        }

        $this->sendResponse($json);
    }

    /*
    * PRODUCT IMAGE MANAGEMENT FUNCTIONS
    */
    public function productimages() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //upload and save image
            if (!empty($this->request->get['other']) && $this->request->get['other'] == 1) {
                $this->addProductImage($this->request);
            } else {
                $this->updateProductImage($this->request);
            }
        }
    }

    /*
    * Upload and save product image
    */
    public function addProductImage($request) {

        $json = array('success' => false);

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        if (ctype_digit($request->get['id'])) {
            $product = $this->model_catalog_product->getProduct($request->get['id']);
            //check product exists
            if(!empty($product)) {
                if(isset($request->files['file'])){
                    $uploadResult = $this->upload($request->files['file'], "products");
                    if(!isset($uploadResult['error'])){
                        $json['success']     = true;
                        $this->model_rest_restadmin->addProductImage($request->get['id'], $uploadResult['file_path']);
                    }else{
                        $json['error']    = $uploadResult['error'];
                    }
                } else {
                    $json['error']	= "File is required!";
                }
            }else {
                $json['success']	= false;
                $json['error']      = "The specified product does not exist.";
            }
        } else {
            $json['success']    = false;
        }

        $this->sendResponse($json);
    }

    /*
    * Upload and update product image
    */
    public function updateProductImage($request) {

        $json = array('success' => false);

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        if (ctype_digit($request->get['id'])) {
            $product = $this->model_catalog_product->getProduct($request->get['id']);
            //check product exists
            if(!empty($product)) {
                if(isset($request->files['file'])){
                    $uploadResult = $this->upload($request->files['file'], "products");
                    if(!isset($uploadResult['error'])){
                        $json['success']     = true;
                        $this->model_rest_restadmin->setProductImage($request->get['id'], $uploadResult['file_path']);
                    }else{
                        $json['error']	= $uploadResult['error'];
                    }
                } else {
                    $json['error']	= "File is required!";
                }
            }else {
                $json['success']	= false;
                $json['error']      = "The specified product does not exist.";
            }
        } else {
            $json['success']    = false;
        }

        $this->sendResponse($json);
    }


    /*
    * Update products quantity
    */
    public function productquantity() {

        $this->checkPlugin();

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            //update products
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && count($requestjson) > 0) {
                $this->updateProductsQuantity($requestjson);
            } else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid request method, use PUT method.";
            $this->sendResponse($json);
        }
    }

    /*
    * Update products quantity
    */
    private function updateProductsQuantity($products)
    {

        $json = array('success' => true);

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');

        foreach ($products as $productItem) {

            if (isset($productItem['product_id']) && ctype_digit($productItem['product_id'])) {
                //if don't update product option quantity, product quantity must be set
                if(!isset($productItem['product_option'])){
                    if(!isset($productItem['quantity']) || !ctype_digit($productItem['quantity'])) {
                        $json['success'] = false;
                        $json['error'] = "Invalid quantity:".$productItem['quantity'].", product id:".$productItem['product_id'];
                    }
                } else {
                    foreach ($productItem['product_option'][0]['product_option_value'] as $option) {
                        if(!isset($option['quantity']) || !ctype_digit($option['quantity'])) {
                            $json['success'] = false;
                            $json['error'] = "Invalid quantity:".$option['quantity'].", product id:".$productItem['product_id'];
                            break;
                        }
                    }
                }

                if ($json['success']) {
                    $id = $productItem['product_id'];

                    $product = $this->model_rest_restadmin->checkProductExists($id);

                    if (!empty($product)) {
                        $this->model_rest_restadmin->editProductQuantity($id, $productItem);
                    } else {
                        $json['success'] = false;
                        $json['error'] = "The specified product does not exist, id: ".$productItem['product_id'];
                    }
                }
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid product id:".$productItem['product_id'];
            }
        }

        $this->sendResponse($json);
    }

    //Image upload
    public function upload($uploadedFile, $subdirectory) {
        $this->language->load('product/product');

        $result = array();


        if (!empty($uploadedFile['name'])) {
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($uploadedFile['name'], ENT_QUOTES, 'UTF-8')));

            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                $result['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array(
                'jpg',
                'jpeg',
                'gif',
                'png'
            );

            if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                $result['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types
            $allowed = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
                'image/gif'
            );

            if (!in_array($uploadedFile['type'], $allowed)) {
                $result['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($uploadedFile['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $result['error'] = $this->language->get('error_filetype');
            }

            if ($uploadedFile['error'] != UPLOAD_ERR_OK) {
                $result['error'] = $this->language->get('error_upload_' . $uploadedFile['error']);
            }
        } else {
            $result['error'] = $this->language->get('error_upload');
        }

        if (!$result && is_uploaded_file($uploadedFile['tmp_name']) && file_exists($uploadedFile['tmp_name'])) {
            $file = basename($filename) . '.' . md5(mt_rand());

            // Hide the uploaded file name so people can not link to it directly.
            $result['file'] = $this->encryption->encrypt($file);

            $result['file_path'] = "catalog/".$subdirectory."/".$filename;
            if($this->rmkdir(DIR_IMAGE."catalog/".$subdirectory)){
                move_uploaded_file($uploadedFile['tmp_name'], DIR_IMAGE .$result['file_path']);
            }else{
                $result['error'] = "Could not create directory or directory is not writeable: ".DIR_IMAGE ."catalog/".$subdirectory;
            }
            $result['success'] = $this->language->get('text_upload');
        }
        return $result;

    }
    private function sendResponse($json)
    {
        $this->response->setOutput(json_encode($json));
    }


    //date format validator
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /*
     * Makes directory and returns BOOL(TRUE) if exists OR made.
     */
    function rmkdir($path, $mode = 0777) {

        if (!file_exists($path)) {
            $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
            $e = explode("/", ltrim($path, "/"));
            if(substr($path, 0, 1) == "/") {
                $e[0] = "/".$e[0];
            }
            $c = count($e);
            $cp = $e[0];
            for($i = 1; $i < $c; $i++) {
                if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                    return false;
                }
                $cp .= "/".$e[$i];
            }
            return @mkdir($path, $mode);
        }

        if (is_writable($path)) {
            return true;
        }else {
            return false;
        }
    }


}

if( !function_exists('apache_request_headers') ) {
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);

                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }

        return( $arh );
    }
}
?>
