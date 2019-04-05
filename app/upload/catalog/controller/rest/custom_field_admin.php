<?php
/**
 * custom_field_admin.php
 *
 * Custom field management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestCustomFieldAdmin extends Controller {

    /*
    * Get custom fields
    */
    public function listCustomFields($request) {

        $json = array('success' => false);

        $this->load->language('restapi/attribute');
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

        /*group parameter*/
        if (isset($request->get['group']) && ctype_digit($request->get['group'])) {
            $parameters["filter_attribute_group_id"] = $request->get['group'];
        }

        $parameters["start"] = ($parameters["start"] - 1) * $parameters["limit"];

        $customfields = array();

        $results = $this->model_rest_restadmin->getCustomFields($parameters);

        foreach ($results as $result) {
            $languageId = isset($result['language_id']) ? $result['language_id'] : (int)$this->config->get('config_language_id');
            $customfields['customfields'][$result['custom_field_id']][] = array(
                'custom_field_id'       => $result['custom_field_id'],
                'name'                  => $result['name'],
                'value'                 => $result['value'],
                'type'                  => $result['type'],
                'location'              => $result['location'],
                'status'                => $result['status'],
                'language_id'           => $result['language_id'],
                'sort_order'            => $result['sort_order'],
                'language_id'           => $languageId,
                'values'           => $this->model_rest_restadmin->getCustomFieldValues($result['custom_field_id'], $languageId)
            );

        }

        if (count($customfields) == 0 || empty($customfields)) {
            $json['error'] = "No product custom field found";
        } else {
            $json['success'] = true;
            $json['data'] = $customfields['customfields'];
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
    * CUSTOM FIELD FUNCTIONS
    * index.php?route=rest/custom_field_admin/customfield
    */
    public function customfield() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listCustomFields($this->request);
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
