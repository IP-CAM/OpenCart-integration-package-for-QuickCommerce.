<?php
/**
 * coupon_admin.php
 *
 * Coupon management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestCouponAdmin extends Controller {

    private static $defaultFields = array(
        "name",
        "code",
        "date_start",
        "date_end",
        "type",
        "discount",
        "total",
        "logged",
        "shipping",
        "uses_total",
        "uses_customer",
        "coupon_product",
        "coupon_category",
        "status",
    );

    private static $defaultFieldValues = array(
        "discount"=>0,
        "total"=>0,
        "uses_total"=>1,
        "uses_customer"=>1,
        "status"=>1,
        "coupon_product"=>array(),
        "coupon_category"=>array(),
    );

    /*
    * Get coupons
    */
    public function listCoupon($request) {

        $json = array('success' => false);

        $this->load->language('restapi/coupon');
        $this->load->model('rest/restadmin');

        $parameters = array(
            "limit" => $this->config->get('config_limit_admin'),
            "start" => 1,
        );

        /*check limit parameter*/
        if (isset($request->get['limit']) && ctype_digit($request->get['limit'])) {
            $parameters["limit"] = $request->get['limit'];
        }

        /*check page parameter*/
        if (isset($request->get['page']) && ctype_digit($request->get['page'])) {
            $parameters["start"] = $request->get['page'];
        }

        $parameters["start"] = ($parameters["start"] - 1) * $parameters["limit"];

        $coupons = array();

        $results = $this->model_rest_restadmin->getCoupons($parameters);
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');

        foreach ($results as $result) {
            $products = $this->model_rest_restadmin->getCouponProducts($result["coupon_id"]);

            $coupon_product = array();

            foreach ($products as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);

                if ($product_info) {
                    $coupon_product[] = array(
                        'product_id' => $product_info['product_id'],
                        'name'       => $product_info['name']
                    );
                }
            }

            $categories = $this->model_rest_restadmin->getCouponCategories($result["coupon_id"]);

            $coupon_category = array();

            foreach ($categories as $category_id) {
                $category_info = $this->model_catalog_category->getCategory($category_id);

                if ($category_info) {
                    $coupon_category[] = array(
                        'category_id' => $category_info['category_id'],
                        'name'        => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
                    );
                }
            }

            $coupons['coupons'][] = array(
                'coupon_id'  => $result['coupon_id'],
                'name'       => $result['name'],
                'code'       => $result['code'],
                'discount'   => $result['discount'],
                'total'      => $result['total'],
                'type'       => $result['type'],
                'logged'     => $result['logged'],
                'shipping'   => $result['shipping'],
                'uses_total' => $result['uses_total'],
                'uses_customer'   => $result['uses_customer'],
                'categories' => $coupon_category,
                'products'   => $coupon_product,
                'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
                'date_end'   => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
                'status'     => $result['status']
            );
        }

        if (count($coupons) == 0 || empty($coupons)) {
            $json['error'] = "No coupons found";
        } else {
            $json['success'] = true;
            $json['data'] = $coupons['coupons'];
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * delete coupon
    {
        "coupons": [8, 9]
    }
    */
    public function deleteCoupon($post) {

        $json = array('success' => true);

        $this->load->language('restapi/coupon');
        $this->load->model('rest/restadmin');

        if (isset($post['coupons'])) {
            foreach ($post['coupons'] as $coupon_id) {
                $this->model_rest_restadmin->deleteCoupon($coupon_id);
            }
        } else {
            $json['error'] = "Error";
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Add coupon
     *
    {
        "name": "demo coupon 2",
        "code": "demo",
        "discount": "20",
        "total": "100",
        "coupon_category": [28],
        "coupon_product": [42],
        "date_start": "2015-02-20",
        "date_end": "2015-03-30",
        "status": "1",
        "type": "P",
        "logged": 1,
        "shipping": 0,
        "uses_total": 1,
        "uses_customer": 1
    }
    */




    public function addCoupon($post) {

        $json = array('success' => true);

        $this->load->language('restapi/coupon');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {

            $this->loadData($post);

            $retval  =$this->model_rest_restadmin->addCoupon($post);
            $json["data"]["id"] = $retval;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Edit coupon
     *
    {
        "name": "demo coupon 2",
        "code": "demo",
        "discount": "20",
        "coupon_category": [28],
        "coupon_product": [42],
        "date_start": "2015-02-20",
        "date_end": "2015-03-30",
        "status": "0",
        "type": "P",
        "logged": 0,
        "shipping": 0,
        "uses_total": 0,
        "uses_customer": 0
    }
    */
    public function editCoupon($id, $post) {

        $json = array('success' => true);

        $this->load->language('restapi/coupon');
        $this->load->model('rest/restadmin');

        $data = $this->model_rest_restadmin->getCoupon($id);

        $this->loadData($post, $data);

        $error = $this->validateForm($post, $id);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editCoupon($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post, $coupon_id=null) {
        $this->load->model('rest/restadmin');
        $error  = array();

        if ((utf8_strlen($post['name']) < 3) || (utf8_strlen($post['name']) > 128)) {
            $error['name'] = $this->language->get('error_name');
        }

        if ((utf8_strlen($post['code']) < 3) || (utf8_strlen($post['code']) > 10)) {
            $error['code'] = $this->language->get('error_code');
        }

        $coupon_info = $this->model_rest_restadmin->getCouponByCode($post['code']);
        if ($coupon_info) {
            if (empty($coupon_id)) {
                $error['warning'] = $this->language->get('error_exists');
            } elseif ($coupon_info['coupon_id'] != $coupon_id)  {
                $error['warning'] = $this->language->get('error_exists');
            }
        }

        return $error;
    }

    /*
    * COUPON FUNCTIONS
    * index.php?route=rest/coupon_admin/coupon
    */
    public function coupon() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listCoupon($this->request);

        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addCoupon($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editCoupon($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["coupons"])) {
                $this->deleteCoupon($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    private function sendResponse($json)
    {
        $this->response->setOutput(json_encode($json));
    }

    private function loadData(&$data, $item=null) {
        foreach(self::$defaultFields as $field){
            if(!isset($data[$field])){
                if(!empty($item) && isset($item[$field])){
                    $data[$field] = $item[$field];
                } else {
                    if(!isset(self::$defaultFieldValues[$field])){
                        if($field == "date_start" || $field == "date_end"){
                            $data[$field] = date('Y-m-d', time());
                        }
                        $data[$field] = "";
                    } else {
                        $data[$field] = self::$defaultFieldValues[$field];
                    }
                }
            }
        }
    }

    private function checkPlugin() {

        $this->config->set('config_error_display', 0);

        $this->response->addHeader('Content-Type: application/json');

        $json = array("success"=>false);

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