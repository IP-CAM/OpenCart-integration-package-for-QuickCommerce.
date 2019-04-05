<?php
/**
 * order_admin.php
 *
 * Order management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestOrderAdmin extends Controller {

    /*
    * create order
{
    "store_id": "0",
    "customer": {
        "customer_id": "32",
        "customer_group_id" : 1,
        "firstname": "Test",
        "lastname": "User",
        "telephone": "+36306668884",
        "fax": "1",
        "email": "test12@test.com",
        "custom_field": ""
    },
    "payment_address": {
        "firstname": "Test",
        "lastname": "User",
        "company": "company",
        "company_id": "company",
        "tax_id": "1",
        "address_1": "Test street 88",
        "address_2": "test",
        "postcode": "1111",
        "city": "Berlin",
        "zone_id": "1433",
        "zone": "Budapest",
        "zone_code": "BU",
        "country_id": "97",
        "country": "Hungary"
    },
    "payment_method": {
        "title": "Cash On Delivery",
        "code": "cod"
    },
    "shipping_address": {
        "firstname": "Test",
        "lastname": "User 2",
        "company": "company",
        "address_1": "Kossuth Lajos út 88",
        "address_2": "test",
        "postcode": "1111",
        "city": "Budapest",
        "zone_id": "1433",
        "zone": "Budapest",
        "zone_code": "BU",
        "country_id": "97",
        "country": "Hungary"
    },
    "shipping_method": {
        "title": "Flat Shipping Rate",
        "code": "flat.flat"
    },
    "vouchers": [
        {
            "description": "description",
            "to_name": "to_name",
            "to_email": "to_email",
            "from_name": "from_name",
            "from_email": "from_email",
            "voucher_theme_id": "voucher_theme_id",
            "message": "message",
            "amount": "amount"
        }
    ],
    "comment": "test comment",
    "affiliate_id": "",
    "commission": "",
    "marketing_id": "",
    "tracking": "",
    "products": [
        {
            "product_id": "46",
            "quantity": "3",
            "price": "10",
            "total": "10",
            "name": "Sony VAIO",
            "model": "Product 19",
            "tax_class_id": "10",
            "reward": "0",
            "subtract": "0",
            "download": "",
            "option": [
                {
                    "product_option_id": "product_option_id",
                    "product_option_value_id": "product_option_value_id",
                    "option_id": "option_id",
                    "option_value_id": "option_value_id",
                    "name": "name",
                    "value": "value",
                    "type": "type"
                }
            ]
        }
    ]
}
    */
    public function addOrder($post) {

        $json = array('success' => false);
        $error = array();

        // Validate if payment address has been set.
		if (!isset($post['payment_address'])) {
            $error[] = "Payment address is empty";
        }

		// Validate if payment method has been set.
		if (!isset($post['payment_method'])) {
            $error[] = "Payment method is empty";
        }

        $products = $post['products'];

		// Validate cart has products and has stock.
		if ((!count($products) && empty($post['vouchers']))) {
            $error[] = "Product is required";
        }

		// Validate minimum quantity requirements.
		foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            /*if ($product['minimum'] > $product_total) {
                $error[] = "Product with id: ".$product['product_id']." minimum validation failed";
                break;
            }*/
        }

        if (empty($error)) {
            $order_data = array();

            $order_data['totals'] = array();
            $total = 0;
            $taxes = $this->getTaxes($products);

            $this->load->model('extension/extension');

            $sort_order = array();

            $results = $this->model_extension_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('total/' . $result['code']);

                    $this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
                }
            }

            if(isset($post['totals']) && !empty($post['totals'])){
                $order_data['totals'] = array_merge($order_data['totals'], $post['totals']);
            }

            $sort_order = array();

            foreach ($order_data['totals'] as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $order_data['totals']);

            $this->load->language('checkout/checkout');

            $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $order_data['store_id'] = $this->config->get('config_store_id');
            $order_data['store_name'] = $this->config->get('config_name');

            if ($order_data['store_id']) {
                $order_data['store_url'] = $this->config->get('config_url');
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }


            $customer_info = $post["customer"];

            $order_data['customer_id'] = $customer_info['customer_id'];
            $order_data['customer_group_id'] = $customer_info['customer_group_id'];
            $order_data['firstname'] = $customer_info['firstname'];
            $order_data['lastname'] = $customer_info['lastname'];
            $order_data['email'] = $customer_info['email'];
            $order_data['telephone'] = $customer_info['telephone'];
            $order_data['fax'] = $customer_info['fax'];
            $order_data['custom_field'] = isset($customer_info['custom_field']) && !empty($customer_info['custom_field']) ? unserialize($customer_info['custom_field']) : array();

            $order_data['payment_firstname'] = $post['payment_address']['firstname'];
            $order_data['payment_lastname'] = $post['payment_address']['lastname'];
            $order_data['payment_company'] = $post['payment_address']['company'];
            $order_data['payment_address_1'] = $post['payment_address']['address_1'];
            $order_data['payment_address_2'] = $post['payment_address']['address_2'];
            $order_data['payment_city'] = $post['payment_address']['city'];
            $order_data['payment_postcode'] = $post['payment_address']['postcode'];
            $order_data['payment_zone'] = $post['payment_address']['zone'];
            $order_data['payment_zone_id'] = $post['payment_address']['zone_id'];
            $order_data['payment_country'] = $post['payment_address']['country'];
            $order_data['payment_country_id'] = $post['payment_address']['country_id'];
            //$order_data['payment_address_format'] = $post['payment_address']['address_format'];
            $order_data['payment_address_format'] = "";
            $order_data['payment_custom_field'] = (isset($post['payment_address']['custom_field']) ? $post['payment_address']['custom_field'] : array());

            if (isset($post['payment_method']['title'])) {
                $order_data['payment_method'] = $post['payment_method']['title'];
            } else {
                $order_data['payment_method'] = '';
            }

            if (isset($post['payment_method']['code'])) {
                $order_data['payment_code'] = $post['payment_method']['code'];
            } else {
                $order_data['payment_code'] = '';
            }

            if ($post['shipping_address']) {
                $order_data['shipping_firstname'] = $post['shipping_address']['firstname'];
                $order_data['shipping_lastname'] = $post['shipping_address']['lastname'];
                $order_data['shipping_company'] = $post['shipping_address']['company'];
                $order_data['shipping_address_1'] = $post['shipping_address']['address_1'];
                $order_data['shipping_address_2'] = $post['shipping_address']['address_2'];
                $order_data['shipping_city'] = $post['shipping_address']['city'];
                $order_data['shipping_postcode'] = $post['shipping_address']['postcode'];
                $order_data['shipping_zone'] = $post['shipping_address']['zone'];
                $order_data['shipping_zone_id'] = $post['shipping_address']['zone_id'];
                $order_data['shipping_country'] = $post['shipping_address']['country'];
                $order_data['shipping_country_id'] = $post['shipping_address']['country_id'];
                //$order_data['shipping_address_format'] = $post['shipping_address']['address_format'];
                $order_data['shipping_address_format'] = "";
                $order_data['shipping_custom_field'] = (isset($post['shipping_address']['custom_field']) ? $post['shipping_address']['custom_field'] : array());

                if (isset($post['shipping_method']['title'])) {
                    $order_data['shipping_method'] = $post['shipping_method']['title'];
                } else {
                    $order_data['shipping_method'] = '';
                }

                if (isset($post['shipping_method']['code'])) {
                    $order_data['shipping_code'] = $post['shipping_method']['code'];
                } else {
                    $order_data['shipping_code'] = '';
                }
            } else {
                $order_data['shipping_firstname'] = '';
                $order_data['shipping_lastname'] = '';
                $order_data['shipping_company'] = '';
                $order_data['shipping_address_1'] = '';
                $order_data['shipping_address_2'] = '';
                $order_data['shipping_city'] = '';
                $order_data['shipping_postcode'] = '';
                $order_data['shipping_zone'] = '';
                $order_data['shipping_zone_id'] = '';
                $order_data['shipping_country'] = '';
                $order_data['shipping_country_id'] = '';
                $order_data['shipping_address_format'] = '';
                $order_data['shipping_custom_field'] = array();
                $order_data['shipping_method'] = '';
                $order_data['shipping_code'] = '';
            }

            $order_data['products'] = array();

            foreach ($products as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    $option_data[] = array(
                        'product_option_id'       => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id'               => $option['option_id'],
                        'option_value_id'         => $option['option_value_id'],
                        'name'                    => $option['name'],
                        'value'                   => $option['value'],
                        'type'                    => $option['type']
                    );
                }

                $order_data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'option'     => $option_data,
                    'download'   => (isset($product['download']) && !empty($product['download'])) ? $product['download'] : array(),
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    'price'      => $product['price'],
                    'total'      => $product['total'],
                    'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward'     => $product['reward']
                );
            }

            $order_data['vouchers'] = array();

            // Gift Voucher
            /*$order_data['vouchers'] = array();

            if (!empty($post['vouchers'])) {
                foreach ($post['vouchers'] as $voucher) {
                    $order_data['vouchers'][] = array(
                        'description'      => $voucher['description'],
                        'code'             => substr(md5(mt_rand()), 0, 10),
                        'to_name'          => $voucher['to_name'],
                        'to_email'         => $voucher['to_email'],
                        'from_name'        => $voucher['from_name'],
                        'from_email'       => $voucher['from_email'],
                        'voucher_theme_id' => $voucher['voucher_theme_id'],
                        'message'          => $voucher['message'],
                        'amount'           => $voucher['amount']
                    );
                }
            }*/

            $order_data['comment'] = $post['comment'];
            $order_data['total'] = $total;


            $order_data['affiliate_id'] = (isset($post['affiliate_id']) ? $post['affiliate_id'] : 0);
            $order_data['commission'] = (isset($post['commission']) ? $post['commission'] : 0);
            $order_data['marketing_id'] = (isset($post['marketing_id']) ? $post['marketing_id'] : 0);
            $order_data['tracking'] = (isset($post['tracking']) ? $post['tracking'] : '');


            $order_data['language_id'] = $this->config->get('config_language_id');
            $order_data['currency_id'] = $this->currency->getId();
            $order_data['currency_code'] = $this->currency->getCode();
            $order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
            $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $order_data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            $this->load->model('checkout/order');

            $data['order_id'] = $this->model_checkout_order->addOrder($order_data);

            $this->model_checkout_order->addOrderHistory($data['order_id'], $this->config->get('cod_order_status_id'), $order_data['comment']);

            $json["success"] = true;
            $json["data"] = array("id"=>$data['order_id']);

            $this->response->setOutput(json_encode($json));

        } else {
            $json["error"] = $error;
            $this->response->setOutput(json_encode($json));
        }


    }

    private function getTaxes($products) {
        $tax_data = array();

        foreach ($products as $product) {
            if ($product['tax_class_id']) {
                $tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

                foreach ($tax_rates as $tax_rate) {
                    if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                        $tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
                    } else {
                        $tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
                    }
                }
            }
        }

        return $tax_data;
    }

    /*
    * ORDER ADMIN FUNCTIONS
    * index.php?route=rest/order_admin/order
    */
    public function order() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addOrder($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }


    /*
    * ORDER FUNCTIONS
    */
    public function orders() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get order details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getOrder($this->request->get['id']);
            }else {
                //get orders list
                $this->listOrders();
            }
        }else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            //update order data
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->updateOrder($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }


        }else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            //delete order
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->deleteOrder($this->request->get['id']);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    /*
    * List orders
    */
    public function listOrders() {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');

        /*check offset parameter*/
        if (isset($this->request->get['offset']) && $this->request->get['offset'] != "" && ctype_digit($this->request->get['offset'])) {
            $offset = $this->request->get['offset'];
        } else {
            $offset 	= 0;
        }

        /*check limit parameter*/
        if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit 	= 10000;
        }

        /*get all orders of user*/
        $results = $this->model_rest_restadmin->getAllOrders($offset, $limit);

        $orders = array();

        if(count($results)){
            foreach ($results as $result) {

                $product_total = $this->model_rest_restadmin->getTotalOrderProductsByOrderId($result['order_id']);
                $voucher_total = $this->model_rest_restadmin->getTotalOrderVouchersByOrderId($result['order_id']);

                $orders[] = array(
                    'order_id'		=> $result['order_id'],
                    'name'			=> $result['firstname'] . ' ' . $result['lastname'],
                    'status'		=> $result['status'],
                    'date_added'	=> $result['date_added'],
                    'products'		=> ($product_total + $voucher_total),
                    'total'			=> $result['total'],
                    'currency_code'	=> $result['currency_code'],
                    'currency_value'=> $result['currency_value'],
                );
            }

            if(count($orders) == 0){
                $json['success'] 	= false;
                $json['error'] 		= "No orders found";
            }else {
                $json['data'] 	= $orders;
            }

        }else {
            $json['error'] 		= "No orders found";
            $json['success'] 	= false;
        }

        $this->sendResponse($json);
    }

    /*
    * List orders whith details
    */
    public function listorderswithdetails() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            $json = array('success' => true);


            $this->load->model('rest/restadmin');

            /*check limit parameter*/
            if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit 	= 100000;
            }

            if (isset($this->request->get['filter_date_added_from'])) {
                $date_added_from = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_added_from']));
                if($this->validateDate($date_added_from)) {
                    $filter_date_added_from = $date_added_from;
                }
            } else {
                $filter_date_added_from = null;
            }

            if (isset($this->request->get['filter_date_added_on'])) {
                $date_added_on = date('Y-m-d',strtotime($this->request->get['filter_date_added_on']));
                if($this->validateDate($date_added_on, 'Y-m-d')) {
                    $filter_date_added_on = $date_added_on;
                }
            } else {
                $filter_date_added_on = null;
            }


            if (isset($this->request->get['filter_date_added_to'])) {
                $date_added_to = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_added_to']));
                if($this->validateDate($date_added_to)) {
                    $filter_date_added_to = $date_added_to;
                }
            } else {
                $filter_date_added_to = null;
            }

            if (isset($this->request->get['filter_date_modified_on'])) {
                $date_modified_on = date('Y-m-d',strtotime($this->request->get['filter_date_modified_on']));
                if($this->validateDate($date_modified_on, 'Y-m-d')) {
                    $filter_date_modified_on = $date_modified_on;
                }
            } else {
                $filter_date_modified_on = null;
            }

            if (isset($this->request->get['filter_date_modified_from'])) {
                $date_modified_from = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_modified_from']));
                if($this->validateDate($date_modified_from)) {
                    $filter_date_modified_from = $date_modified_from;
                }
            } else {
                $filter_date_modified_from = null;
            }

            if (isset($this->request->get['filter_date_modified_to'])) {
                $date_modified_to = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_modified_to']));
                if($this->validateDate($date_modified_to)) {
                    $filter_date_modified_to = $date_modified_to;
                }
            } else {
                $filter_date_modified_to = null;
            }

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                $filter_order_status_id = $this->request->get['filter_order_status_id'];
            } else {
                $filter_order_status_id = null;
            }

            $data = array(
                'filter_date_added_on'      => $filter_date_added_on,
                'filter_date_added_from'    => $filter_date_added_from,
                'filter_date_added_to'      => $filter_date_added_to,
                'filter_date_modified_on'   => $filter_date_modified_on,
                'filter_date_modified_from' => $filter_date_modified_from,
                'filter_date_modified_to'   => $filter_date_modified_to,
                'filter_order_status_id'    => $filter_order_status_id,
                'start'						=> ($page - 1) * $limit,
                'limit'						=> $limit
            );


            $results = $this->model_rest_restadmin->getOrdersByFilter($data);
            /*get all orders*/
            //$results = $this->model_account_order->getAllOrders($offset, $limit);

            $orders = array();

            if(count($results)){

                foreach ($results as $result) {

                    $orderData = $this->getOrderDetailsToOrder($result);

                    if (!empty($orderData)) {
                        $orders[] = $orderData;
                    }
                }

                if(count($orders) == 0){
                    $json['success'] 	= false;
                    $json['error'] 		= "No orders found";
                }else {
                    $json['data'] 	= $orders;
                }

            }else {
                $json['error'] 		= "No orders found";
                $json['success'] 	= false;
            }
        }else{
            $json['success'] 	= false;
        }

        $this->sendResponse($json);
    }

    /*Get order details*/
    public function getOrder($order_id) {

        $this->load->model('checkout/order');
        $this->load->model('account/order');

        $json = array('success' => true);

        if (ctype_digit($order_id)) {
            $order_info = $this->model_checkout_order->getOrder($order_id);

            if (!empty($order_info)) {
                $json['success'] 	= true;
                $json['data'] 		= $this->getOrderDetailsToOrder($order_info);

            } else {
                $json['success']     = false;
                $json['error']       = "The specified order does not exist.";

            }
        } else {
            $json['success']     = false;
            $json['error']       = "Invalid order id";

        }

        $this->sendResponse($json);
    }

    /*Get all orders of user */
    public function userorders(){

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            $json = array('success' => true);

            $user = null;

            /*check user parameter*/
            if (isset($this->request->get['user']) && $this->request->get['user'] != "" && ctype_digit($this->request->get['user'])) {
                $user = $this->request->get['user'];
            } else {
                $json['success'] 	= false;
            }

            if($json['success'] == true){
                $orderData['orders'] = array();

                $this->load->model('rest/restadmin');

                /*get all orders of user*/
                $results = $this->model_rest_restadmin->getOrdersByUser($user);

                $orders = array();

                foreach ($results as $result) {

                    $product_total = $this->model_rest_restadmin->getTotalOrderProductsByOrderId($result['order_id']);
                    $voucher_total = $this->model_rest_restadmin->getTotalOrderVouchersByOrderId($result['order_id']);

                    $orders[] = array(
                        'order_id'		=> $result['order_id'],
                        'name'			=> $result['firstname'] . ' ' . $result['lastname'],
                        'status'		=> $result['status'],
                        'date_added'	=> $result['date_added'],
                        'products'		=> ($product_total + $voucher_total),
                        'total'			=> $result['total'],
                        'currency_code'	=> $result['currency_code'],
                        'currency_value'=> $result['currency_value'],
                    );
                }

                if(count($orders) == 0){
                    $json['success'] 	= false;
                    $json['error'] 		= "No orders found";
                }else {
                    $json['data'] 	= $orders;
                }
            }else{
                $json['success'] 	= false;
            }
        }

        $this->sendResponse($json);
    }
    private function getOrderDetailsToOrder($order_info) {

        $this->load->model('catalog/product');
        $this->load->model('rest/restadmin');
        $this->load->model('account/order');

        $orderData = array();

        if (!empty($order_info)) {
            foreach($order_info as $key=>$value){
                $orderData[$key] = $value;
            }

            $orderData['products'] = array();

            $products = $this->model_account_order->getOrderProducts($orderData['order_id']);

            foreach ($products as $product) {
                $option_data = array();

                $options = $this->model_rest_restadmin->getOrderOptions($orderData['order_id'], $product['order_product_id']);

                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => $option['value'],
                            'type'  => $option['type'],
                            'product_option_id'  => isset($option['product_option_id']) ? $option['product_option_id'] : "",
                            'product_option_value_id'  => isset($option['product_option_value_id']) ? $option['product_option_value_id'] : "",
                            'option_id' => isset($option['option_id']) ? $option['option_id'] : "",
                            'option_value_id'  => isset($option['option_value_id']) ? $option['option_value_id'] : ""
                        );
                    } else {
                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
                            'type'  => $option['type']
                        );
                    }
                }

                $origProduct = $this->model_catalog_product->getProduct($product['product_id']);

                $orderData['products'][] = array(
                    'order_product_id' => $product['order_product_id'],
                    'product_id'       => $product['product_id'],
                    'name'    	 	   => $product['name'],
                    'model'    		   => $product['model'],
                    'sku'			   => (!empty($origProduct['sku']) ? $origProduct['sku'] : "") ,
                    'option'   		   => $option_data,
                    'quantity'		   => $product['quantity'],
                    'currency_code'	   => $order_info['currency_code'],
                    'currency_value'   => $order_info['currency_value'],
                    'price_formated'   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'price'    		   => $product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0),
                    'total_formated'   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total'    		   => $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0)
                );
            }
        }

        $orderData['histories'] = array();

        $histories = $this->model_rest_restadmin->getOrderHistories($orderData['order_id'],0,1000 );

        foreach ($histories as $result) {
            $orderData['histories'][] = array(
                'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
                'status'     => $result['status'],
                'comment'    => nl2br($result['comment']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        $vouchers = $this->model_rest_restadmin->getOrderVouchers($orderData['order_id']);

        foreach ($vouchers as $voucher) {
            $orderData['vouchers'][] = array(
                'description' => $voucher['description'],
                'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
            );
        }

        $totals = $this->model_rest_restadmin->getOrderTotals($orderData['order_id']);

        foreach ($totals as $total) {
            $orderData['totals'][] = array(
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
            );
        }

        return $orderData;
    }

    /*
        Update order status

    */
    public function updateOrder($id, $data) {


        $json = array('success' => false);

        $this->load->model('checkout/order');

        if (ctype_digit($id)) {

            if (isset($data['status']) && ctype_digit($data['status'])) {

                $result = $this->model_checkout_order->getOrder($id);
                if(!empty($result)) {
                    $json['success']     = true;
                    $this->model_checkout_order->addOrderHistory($id, $data['status']);
                }else {
                    $json['success']     = false;
                    $json['error']       = "The specified order does not exist.";
                }

            } else {
                $json['success'] 	= false;
            }
        } else {
            $json['success']     = false;
        }

        $this->sendResponse($json);
    }

    /*Delete order*/
    public function deleteOrder($id) {

        $json['success']     = false;

        $this->load->model('checkout/order');

        if (ctype_digit($id)) {
            $result = $this->model_checkout_order->getOrder($id);

            if(!empty($result)) {
                $json['success']     = true;
                // Void the order first
                $this->model_checkout_order->addOrderHistory($id, 0);

                $this->model_checkout_order->deleteOrder($id);
                // Gift Voucher
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->disableVoucher($id);
            }else{
                $json['success']     = false;
                $json['error']       = "The specified order does not exist.";
            }

        }else {
            $json['success']     = false;
        }

        $this->sendResponse($json);
    }


    /*
    * Update order status by status name
    */
    public function orderstatus() {

        $this->checkPlugin();
        if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
            ) {
                $requestjson = file_get_contents('php://input');

                $requestjson = json_decode($requestjson, true);

                $this->updateOrderStatusByName($this->request->get['id'], $requestjson);
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid request, please set order id and order status";

                $this->sendResponse($json);
            }
        }
    }

    /*
     *   Update order status by status name
    */
    public function updateOrderStatusByName($id, $data)
    {

        $json = array('success' => false);

        $this->load->model('checkout/order');

        if (ctype_digit($id)) {
            if (isset($data['status']) && ($data['status']) != "") {

                $status = $this->findStatusByName($data['status']);

                if ($status) {
                    $result = $this->model_checkout_order->getOrder($id);
                    if (!empty($result)) {
                        $json['success'] = true;
                        $this->model_checkout_order->addOrderHistory($id, $status);
                    } else {
                        $json['success'] = false;
                        $json['error'] = "The specified order does not exist.";
                    }
                } else {
                    $json['success'] = false;
                    $json['error'] = "The specified status does not exist.";
                }
            } else {
                $json['success'] = false;
                $json['error'] = "Invalid status id";
            }
        } else {
            $json['success'] = false;
            $json['error'] = "Invalid order id";
        }

        $this->sendResponse($json);

    }

    private function findStatusByName($status_name)
    {
        $this->load->model('rest/restadmin');

        $status_id = $this->model_rest_restadmin->getOrderStatusByName($status_name);
        return ((count($status_id) > 0 && $status_id[0]['order_status_id']) ? $status_id[0]['order_status_id'] : false );
    }

    /*
    * ADD ORDER HISTORY
    */
    public function orderhistory() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->addOrderHistory($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    private function addOrderHistory($id, $data) {

        $json = array('success' => true);

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($id);

        if ($order_info) {
            $this->model_checkout_order->addOrderHistory($id, $data['order_status_id'], $data['comment'], $data['notify']);
        } else {
            $json["success"] = false;
            $json["error"] = "Order not found";
        }

        $this->response->setOutput(json_encode($json));
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

    //date format validator
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function sendResponse($json)
    {
        $this->response->setOutput(json_encode($json));
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
