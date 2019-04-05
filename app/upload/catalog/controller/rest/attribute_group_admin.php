<?php
/**
 * attribute_group_admin.php
 *
 * Attribute group management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestAttributeGroupAdmin extends Controller {

    private static $defaultFields = array(
        "attribute_description",
        "sort_order"
    );

    private static $defaultFieldValues = array(
        "attribute_description"=>array()
    );

    /*
    * Get attribute groups
    */
    public function listAttributeGroups($request) {

        $json = array('success' => false);

        $this->load->language('restapi/attribute_group');
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

        $results = $this->model_rest_restadmin->getAttributeGroups($parameters);

        $attribute_groups = array();

        foreach ($results as $result) {
            $languageId = isset($result['language_id']) ? $result['language_id'] : (int)$this->config->get('config_language_id');
            $attribute_groups['attribute_groups'][$result['attribute_group_id']][] = array(
                'attribute_group_id' => $result['attribute_group_id'],
                'name'               => $result['name'],
                'sort_order'         => $result['sort_order'],
                'language_id'      => $languageId
            );
        }

        if (count($attribute_groups) == 0 || empty($attribute_groups)) {
            $json['error'] = "No product attribute group found";
        } else {
            $json['success'] = true;
            $json['data'] = $attribute_groups['attribute_groups'];
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
    * delete attribute groups
    {
        "groups": [8, 9 ]
    }
    */
    public function deleteAttributeGroup($post) {

        $json = array('success' => true);

        $this->load->language('restapi/attribute_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateDelete($post);

        if (isset($post['groups']) && empty($error)) {
            foreach ($post['groups'] as $attribute_group_id) {
                $this->model_rest_restadmin->deleteAttributeGroup($attribute_group_id);
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
    * Add attribute group
     *
      {
            "sort_order": 1,
            "attribute_group_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA 2"
                }
            ]
      }
    */
    public function addAttributeGroup($post) {

        $json = array('success' => true);

        $this->load->language('restapi/attribute_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
            foreach(self::$defaultFields as $field){
                if(!isset($post[$field])){
                    if(!isset(self::$defaultFieldValues[$field])){
                        $post[$field] = "";
                    } else {
                        $post[$field] = self::$defaultFieldValues[$field];
                    }
                }
            }
            $retval = $this->model_rest_restadmin->addAttributeGroup($post);
            $json["data"]["id"] = $retval;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Edit attribute group
     *
      {
            "sort_order": 1,
            "attribute_group_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA 2"
                }
            ]
      }
    */
    public function editAttributeGroup($id, $post) {

        $json = array('success' => true);

        $this->load->language('restapi/attribute_group');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editAttributeGroup($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post) {

        $error  = array();

        foreach ($post['attribute_group_description'] as $attribute_group_description) {
            if ((utf8_strlen($attribute_group_description['name']) < 3) || (utf8_strlen($attribute_group_description['name']) > 64)) {
                $error['name'][$attribute_group_description['language_id']] = $this->language->get('error_name');
            }
        }

        return $error;
    }

    protected function validateDelete($post) {

        $this->load->model('catalog/product');

        $error  = array();

        foreach ($post['groups'] as $attribute_group_id) {
            $attribute_total = $this->model_rest_restadmin->getTotalAttributesByAttributeGroupId($attribute_group_id);

            if ($attribute_total) {
                $error['warning'] = sprintf($this->language->get('error_attribute'), $attribute_total);
            }
        }

        return $error;
    }

    /*
    * ATTRIBUTE GROUP FUNCTIONS
    * index.php?route=rest/attribute_group_admin/attributegroup
    */
    public function attributegroup() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listAttributeGroups($this->request);
        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addAttributeGroup($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editAttributeGroup($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["groups"])) {
                $this->deleteAttributeGroup($requestjson);
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
