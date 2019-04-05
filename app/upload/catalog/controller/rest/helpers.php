<?php
/**
 * helpers.php
 *
 * Helper informations
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestHelpers extends Controller {

    /*
    * GET UTC AND LOCAL TIME DIFFERENCE
    * returns offset in seconds
    */
    public function utc_offset() {

        $this->checkPlugin();

        $json = array('success' => false);

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $serverTimeZone = date_default_timezone_get();
            $timezone = new DateTimeZone($serverTimeZone);
            $now = new DateTime("now", $timezone);
            $offset = $timezone->getOffset($now);

            $json['data'] = array('offset' => $offset);
            $json['success'] = true;
        }

        $this->response->setOutput(json_encode($json));
    }
    /*check database modification*/
    public function getchecksum() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            $this->load->model('rest/restadmin');

            $checksum = $this->model_rest_restadmin->getChecksum();

            $checksumArray = array();

            for ($i = 0; $i<count($checksum);$i++){
                $checksumArray[] = array('table' => $checksum[$i]['Table'], 'checksum' => $checksum[$i]['Checksum']);
            }

            $json = array('success' => true,'data' => $checksumArray);

            $this->response->setOutput(json_encode($json));
        }
    }


    /*
    * PRODUCT SPECIFIC INFOS
    */
    public function productclasses() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $json = array('success' => true);

            $this->load->model('rest/restadmin');

            $json['data']['stock_statuses'] = $this->model_rest_restadmin->getStockStatuses();
            $json['data']['length_classes'] = $this->model_rest_restadmin->getLengthClasses();
            $json['data']['weight_classes'] = $this->model_rest_restadmin->getWeightClasses();
            $json['data']['tax_rates']      = $this->model_rest_restadmin->getTaxRates();


            $stores_result = $this->model_rest_restadmin->getStores();

            $stores = array();

            foreach ($stores_result as $result) {
                $stores[] = array(
                    'store_id'	=> $result['store_id'],
                    'name'      => $result['name']
                );
            }

            $default_store[] = array(
                'store_id'	=> 0,
                'name'      => $this->config->get('config_name')
            );

            $json['data']['stores'] = array_merge($default_store, $stores);

            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            if(count($languages) == 0){
                $json['data']['languages'] = array();
            }else {
                $json['data']['languages'] = $languages;
            }

            $this->load->model('localisation/currency');

            $currencies = $this->model_localisation_currency->getCurrencies();

            if(count($currencies) == 0){
                $json['data']['currency'] = array();
            }else {
                $json['data']['currency'] = $languages;
            }

            $orderStatuses = $this->model_rest_restadmin->getOrderStatuses();

            if(count($orderStatuses) == 0){
                $json['data']['order_statuses'] = array();
            }else {
                $json['data']['order_statuses'] = $orderStatuses;
            }

            $json['data']['recurrings'] = $this->model_rest_restadmin->getRecurrings();

            $this->response->setOutput(json_encode($json));
        } else{
            $this->response->setOutput(json_encode(array('success' => false)));
        }
    }
    /*
    * COUNTRY FUNCTIONS
    */
    public function countries() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get country details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCountry($this->request->get['id']);
            }else {
                $this->listCountries();
            }
        }
    }

    /*
    * Get countries
    */
    private function listCountries() {

        $json = array('success' => true);

        $this->load->model('localisation/country');

        $results = $this->model_localisation_country->getCountries();

        $data = array();

        foreach ($results as $country) {
            $data[] = $this->getCountryInfo($country, false);
        }

        if(count($results) == 0){
            $json['success'] 	= false;
            $json['error'] 		= "No country found";
        }else {
            $json['data'] 		= $data;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Get country details
    */
    public function getCountry($country_id) {

        $json = array('success' => true);

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($country_id);

        if(!empty($country_info)){
            $json["data"] = $this->getCountryInfo($country_info);
        }else {
            $json['success']     = false;
            $json['error']       = "The specified country does not exist.";
        }

        $this->response->setOutput(json_encode($json));
    }

    private function getCountryInfo($country_info, $addZone = true) {
        $this->load->model('localisation/zone');
        $info = array(
            'country_id'        => $country_info['country_id'],
            'name'              => $country_info['name'],
            'iso_code_2'        => $country_info['iso_code_2'],
            'iso_code_3'        => $country_info['iso_code_3'],
            'address_format'    => $country_info['address_format'],
            'postcode_required' => $country_info['postcode_required'],
            'status'            => $country_info['status']
        );
        if($addZone){
            $info['zone'] = $this->model_localisation_zone->getZonesByCountryId($country_info['country_id']);
        }

        return $info;
    }

    /*
    * SESSION FUNCTIONS
    */
    public function session() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get session details
            $this->getSessionId();
        }
    }

    /*
    * Get current session id
    */
    public function getSessionId() {

        $json = array('success' => true);

        $json['data'] = array('session' => session_id());

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
