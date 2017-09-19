<?php
/**
 * customer_admin.php
 *
 * Customer management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestCustomerAdmin extends Controller {

    static $defaultFields = array(
        "firstname",
        "lastname",
        "email",
        "telephone",
        "fax",
        "newsletter",
        "status",
        "approved",
        "safe",
        "customer_group_id",
    );

    static $customerAddressFields = array(
        "firstname",
        "lastname",
        "company",
        "address_1",
        "address_2",
        "city",
        "country_id",
        "postcode",
        "country",
        "zone_id"
    );

    /*
    * Customer FUNCTIONS
    * index.php?route=rest/customer_admin/customers
    */
    public function customers() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get customer details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCustomer($this->request->get['id']);
            }else {
                //get customers list
                $this->listCustomers($this->request);
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addCustomer($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            //update customer
            $requestjson = file_get_contents('php://input');

            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editCustomer($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }

        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["customers"])) {
                $this->deleteCustomer($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    /*
    * Get customers list
    */
    private function listCustomers($request) {

        $json = array('success' => false);

        $this->load->language('restapi/customer');
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

        if (isset($request->get['filter_date_added_from'])) {
            $date_added_from = date('Y-m-d H:i:s',strtotime($request->get['filter_date_added_from']));
            if($this->validateDate($date_added_from)) {
                $filter_date_added_from = $date_added_from;
            }
        } else {
            $filter_date_added_from = null;
        }

        if (isset($request->get['filter_date_added_on'])) {
            $date_added_on = date('Y-m-d',strtotime($request->get['filter_date_added_on']));
            if($this->validateDate($date_added_on, 'Y-m-d')) {
                $filter_date_added_on = $date_added_on;
            }
        } else {
            $filter_date_added_on = null;
        }


        if (isset($request->get['filter_date_added_to'])) {
            $date_added_to = date('Y-m-d H:i:s',strtotime($request->get['filter_date_added_to']));
            if($this->validateDate($date_added_to)) {
                $filter_date_added_to = $date_added_to;
            }
        } else {
            $filter_date_added_to = null;
        }

        $customers = array();

        $parameters['filter_date_added_on']   = $filter_date_added_on;
        $parameters['filter_date_added_from'] = $filter_date_added_from;
        $parameters['filter_date_added_to']   = $filter_date_added_to;

        $results = $this->model_rest_restadmin->getCustomers($parameters);

        foreach ($results as $result) {

            $addresses = $this->model_rest_restadmin->getAddresses($result['customer_id']);
            $custom_fields = $this->model_rest_restadmin->getCustomFields($this->config->get('config_customer_group_id'));
            $account_custom_field = unserialize($result['custom_field']);

            $customers['customers'][] = array(
                'customer_id'    => $result['customer_id'],
                'customer_group_id'    => $result['customer_group_id'],
                'name'           => $result['name'],
                'email'          => $result['email'],
                'customer_group' => $result['customer_group'],
                'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'ip'             => $result['ip'],
                'account_custom_field'    => $account_custom_field,
                'custom_fields'           => $custom_fields,
                'addresses'      => $addresses,
                'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
            );
        }

        if (count($customers) == 0 || empty($customers)) {
            $json['error'] = "No customer found";
        } else {
            $json['success'] = true;
            $json['data'] = $customers['customers'];
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    /*
    * Get customer details
    */
    private function getCustomer($id) {

        $json = array('success' => true);

        $this->load->model('account/customer');

        if (ctype_digit($id)) {
            $customer = $this->model_account_customer->getCustomer($id);
            if(!empty($customer['customer_id'])){
                $json['data'] = $this->getCustomerInfo($customer);
            }else {
                $json['success']     = false;
                $json['error']       = "The specified customer does not exist.";
            }
        } else {
            $json['success'] 	= false;
        }

        $this->response->setOutput(json_encode($json));
    }

    private function getCustomerInfo($customer) {
        // Custom Fields
        $this->load->model('account/custom_field');
        $this->load->model('rest/restadmin');

        $custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));
        $account_custom_field = unserialize($customer['custom_field']);

        $addresses = $this->model_rest_restadmin->getAddresses($customer['customer_id']);

        return array(
            'store_id'                => $customer['store_id'],
            'customer_id'             => $customer['customer_id'],
            'firstname'               => $customer['firstname'],
            'lastname'                => $customer['lastname'],
            'telephone'               => $customer['telephone'],
            'fax'                     => $customer['fax'],
            'email'                   => $customer['email'],
            'customer_group_id'       => $customer['customer_group_id'],
            'addresses'               => $addresses,
            'account_custom_field'    => $account_custom_field,
            'custom_fields'           => $custom_fields

        );
    }

    /*
	Add customer
    {
        "firstname":"firstname",
        "lastname":"lastname",
        "email":"demo@demo.com",
        "password":"password",
        "confirm":"password",
        "telephone":"telephone",
        "fax":"fax",
        "newsletter":"1",
        "status":"1",
        "approved":"1",
        "safe":"1",
        "customer_group_id":1,
        "custom_field":{
            "account":
            {
            "1": "6666555777",
            "2": "1"
            }
    },
    "address":[
        {
            "firstname":"firstname",
            "lastname":"lastname",
            "company":"company name",
            "address_1":"address_1",
            "address_2":"address_2",
            "city":"city",
            "country_id":"1",
            "zone_id":"1",
            "postcode":"3333",
            "country":"india",
            "default":"1"
        },
        {
            "firstname":"firstname",
            "lastname":"lastname",
            "company":"company name",
            "address_1":"address_1",
            "address_2":"address_2",
            "city":"city",
            "country_id":"1",
            "zone_id":"1",
            "postcode":"3333",
            "country":"india"
        }
    ]
}

   */
    public function addCustomer($post) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');
        $this->load->language('restapi/customer');

        $this->loadData($post);

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
            $customerId = $this->model_rest_restadmin->addCustomer($post);
            $json["data"]["id"] = $customerId;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
	Update customer
   */
    public function editCustomer($id, $post) {

        $json = array('success' => true);

        $this->load->language('restapi/customer');
        $this->load->model('rest/restadmin');

        $customer = $this->model_rest_restadmin->getCustomer($id);

        $this->loadData($post, $customer);

        $error = $this->validateForm($post, $id);

        if (!empty($post) && empty($error)) {
            $this->model_rest_restadmin->editCustomer($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }
        $this->response->setOutput(json_encode($json));
    }

    private function loadData(&$post, $customer=null) {
        foreach(self::$defaultFields as $field){
            if(!isset($post[$field])){
                if(!empty($customer) && isset($customer[$field])){
                    $post[$field] = $customer[$field];
                } else {
                    $post[$field] = "";
                }
            }
        }

        foreach(self::$customerAddressFields as $field){
            if(isset($post["address"])){
                foreach($post["address"] as &$address){
                    if(!isset($address[$field])){
                        $address[$field] = "";
                    }
                }
            }
        }

    }

    /*
    * delete customers
    {
        "customers": [8, 9 ]
    }
    */
    public function deleteCustomer($post) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');

        if (isset($post['customers']) && !empty($post['customers'])) {
            foreach ($post['customers'] as $customers) {
                $this->model_rest_restadmin->deleteCustomer($customers);
            }
        } else {
            $json['error'] = "Empty ids array";
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    private function validateForm($post, $customer_id = null) {

        $this->load->model('account/customer');
        $this->load->language('restapi/customer');

        $error = array();

        if ((utf8_strlen($post['firstname']) < 1) || (utf8_strlen(trim($post['firstname'])) > 32)) {
            $error['firstname'] = $this->language->get('error_firstname');
        }

        if ((utf8_strlen($post['lastname']) < 1) || (utf8_strlen(trim($post['lastname'])) > 32)) {
            $error['lastname'] = $this->language->get('error_lastname');
        }

        if ((utf8_strlen($post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $post['email'])) {
            $error['email'] = $this->language->get('error_email');
        }
        
        $customer_info = $this->model_account_customer->getCustomerByEmail($post['email']);

        if (empty($customer_id)) {
            if ($customer_info) {
                $error['warning'] = $this->language->get('error_exists');
            }
        } else {
            if ($customer_info && ($customer_id != $customer_info['customer_id'])) {
                $error['warning'] = $this->language->get('error_exists');
            }
        }

        if ((utf8_strlen($post['telephone']) < 3) || (utf8_strlen($post['telephone']) > 32)) {
            $error['telephone'] = $this->language->get('error_telephone');
        }

        // Custom field validation
        $custom_fields = $this->model_rest_restadmin->getCustomFields(array('filter_customer_group_id' => $post['customer_group_id']));

        foreach ($custom_fields as $custom_field) {
            if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($post['custom_field'][$custom_field['custom_field_id']])) {
                $error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            }
        }

        if (isset($post['password']) || (!isset($customer_id))) {
            if ((utf8_strlen($post['password']) < 4) || (utf8_strlen($post['password']) > 20)) {
                $error['password'] = $this->language->get('error_password');
            }

            if ($post['password'] != $post['confirm']) {
                $error['confirm'] = $this->language->get('error_confirm');
            }
        }

        if (isset($post['address'])) {
            foreach ($post['address'] as $key => $value) {
                if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
                    $error['address'][$key]['firstname'] = $this->language->get('error_firstname');
                }

                if ((utf8_strlen($value['lastname']) < 1) || (utf8_strlen($value['lastname']) > 32)) {
                    $error['address'][$key]['lastname'] = $this->language->get('error_lastname');
                }

                if ((utf8_strlen($value['address_1']) < 3) || (utf8_strlen($value['address_1']) > 128)) {
                    $error['address'][$key]['address_1'] = $this->language->get('error_address_1');
                }

                if ((utf8_strlen($value['city']) < 2) || (utf8_strlen($value['city']) > 128)) {
                    $error['address'][$key]['city'] = $this->language->get('error_city');
                }

                $this->load->model('localisation/country');

                $country_info = $this->model_localisation_country->getCountry($value['country_id']);

                if ($country_info && $country_info['postcode_required'] && (utf8_strlen($value['postcode']) < 2 || utf8_strlen($value['postcode']) > 10)) {
                    $error['address'][$key]['postcode'] = $this->language->get('error_postcode');
                }

                if ($value['country_id'] == '') {
                    $error['address'][$key]['country'] = $this->language->get('error_country');
                }

                if (!isset($value['zone_id']) || $value['zone_id'] == '') {
                    $error['address'][$key]['zone'] = $this->language->get('error_zone');
                }

                foreach ($custom_fields as $custom_field) {
                    if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($value['custom_field'][$custom_field['custom_field_id']])) {
                        $error['address'][$key]['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                    }
                }
            }
        }

        return $error;
    }

    //date format validator
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function checkPlugin() {

        $this->config->set('config_error_display', 0);

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
?>
