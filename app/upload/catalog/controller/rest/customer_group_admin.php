<?php
/**
 * customer_group_admin.php
 *
 * Customer group management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestCustomerGroupAdmin extends Controller {

    /*
    * Get customer groups
    */
    public function listCustomerGroups($request) {

        $json = array('success' => false);

        $this->load->language('restapi/customer_group');
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

        $customer_groups = array();

        $results = $this->model_rest_restadmin->getCustomerGroups($parameters);

        foreach ($results as $result) {
            $languageId = isset($result['language_id']) ? $result['language_id'] : (int)$this->config->get('config_language_id');
            $customer_groups['customer_groups'][$result['customer_group_id']][] = array(
                'customer_group_id' => $result['customer_group_id'],
                'name'              => $result['name'] . (($result['customer_group_id'] == $this->config->get('config_customer_group_id')) ? $this->language->get('text_default') : null),
                'sort_order'        => $result['sort_order'],
                'description'       => $result['description'],
                'language_id'       => $languageId
            );
        }

        if (count($customer_groups) == 0 || empty($customer_groups)) {
            $json['error'] = "No customer group found";
        } else {
            $json['success'] = true;
            $json['data'] = $customer_groups['customer_groups'];
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
    * delete customer groups
    {
        "groups": [8, 9 ]
    }
    */
    public function deleteCustomerGroup($post) {

        $json = array('success' => true);

        $this->load->language('restapi/customer_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateDelete($post);

        if (isset($post['groups']) && empty($error)) {
            foreach ($post['groups'] as $customer_group_id) {
                $this->model_rest_restadmin->deleteCustomerGroup($customer_group_id);
            }
        } else {
            $json['error'] = $error;
            $json["success"] = false;
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
    * Add customer group
     *
      {
            "sort_order": 1,
            "approval": 1,
            "customer_group_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA 2",
                    "description": "SUPER MEGA GIGA description"
                }
            ]
      }
    */
    public function addCustomerGroup($post) {

        $json = array('success' => true);

        $this->load->language('restapi/customer_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
            $retval = $this->model_rest_restadmin->addCustomerGroup($post);
            $json["data"]["id"] = $retval;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Edit customer group
     *
      {
            "sort_order": 1,
            "approval": 1,
            "customer_group_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA 2 mod",
                    "description": "SUPER MEGA GIGA description"
                }
            ]
      }
    */
    public function editCustomerGroup($id, $post) {

        $json = array('success' => true);

        $this->load->language('restapi/customer_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editCustomerGroup($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }
        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post) {

        $error  = array();

        foreach ($post['customer_group_description'] as $customer_group_description) {
            if ((utf8_strlen($customer_group_description['name']) < 3) || (utf8_strlen($customer_group_description['name']) > 64)) {
                $error['name'][$customer_group_description['language_id']] = $this->language->get('error_name');
            }
        }

        return $error;
    }

    protected function validateDelete($post) {

        $error  = array();

        $this->load->model('setting/store');
        $this->load->model('rest/restadmin');

        foreach ($post['groups'] as $customer_group_id) {

            $store_total = $this->model_rest_restadmin->getTotalStoresByCustomerGroupId($customer_group_id);

            if ($store_total) {
                $this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
            }

            $customer_total = $this->model_rest_restadmin->getTotalCustomersByCustomerGroupId($customer_group_id);

            if ($customer_total) {
                $this->error['warning'] = sprintf($this->language->get('error_customer'), $customer_total);
            }
        }

        return $error;
    }

    /*
    * Customer GROUP FUNCTIONS
    * index.php?route=rest/customer_group_admin/customergroup
    */
    public function customergroup() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listCustomerGroups($this->request);
        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addCustomerGroup($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editCustomerGroup($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["groups"])) {
                $this->deleteCustomerGroup($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
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
?>
