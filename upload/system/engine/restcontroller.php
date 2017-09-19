<?php
abstract class RestController extends Controller {

    public function checkPlugin() {
        $this->config->set('config_error_display', 0);

        $this->response->addHeader('Content-Type: application/json');

        $json = array("success"=>false);

        /*check rest api is enabled*/
        /*if (!$this->config->get('rest_api_status')) {
            $json["error"] = 'API is disabled. Enable it!';
        }*/


        $headers = apache_request_headers();

        $key = "";

        if(isset($headers['X-Oc-Merchant-Id'])){
            $key = $headers['X-Oc-Merchant-Id'];
        }else if(isset($headers['X-OC-MERCHANT-ID'])) {
            $key = $headers['X-OC-MERCHANT-ID'];
        }

        /*validate api security key*/
        if ($this->config->get('rest_api_key') && ($key != $this->config->get('rest_api_key'))) {
            $json["error"] = 'Invalid secret key';
        }

        if(isset($json["error"])){
            echo(json_encode($json));
            exit;
        }
        /*$osc_session = "";

        if(isset($headers['X-Oc-Session'])){
            $osc_session = $headers['X-Oc-Session'];
        } else if(isset($headers['X-OC-SESSION'])){
            $osc_session = $headers['X-OC-SESSION'];
        }*/

        if(isset($headers['X-Oc-Store-Id'])){
            $this->config->set('config_store_id', $headers['X-Oc-Store-Id']);
        } else if(isset($headers['X-OC-STORE-ID'])){
            $this->config->set('config_store_id', $headers['X-OC-STORE-ID']);
        }

        /*if(!empty($osc_session)){
            $this->update_session($osc_session);
        }*/


        //set language
        $osc_lang = "";
        if(isset($headers['X-Oc-Merchant-Language'])){
            $osc_lang = $headers['X-Oc-Merchant-Language'];
        }else if(isset($headers['X-OC-MERCHANT-LANGUAGE'])){
            $osc_lang = $headers['X-OC-MERCHANT-LANGUAGE'];
        }

        if($osc_lang != ""){
            $this->session->data['language'] = $osc_lang;
        }
    }

    public function sendResponse($json) {
        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
            echo '</pre>';
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

    //update user session
    function update_session($osc_session) {
        if(session_id() != $osc_session){
            // Close the current session
            session_write_close();
            session_id($osc_session);
            session_start();
            $this->session->data = $_SESSION;
        }
    }

    public function returnDeprecated(){
        $json['success'] = false;
        $json['error'] = "This service has been removed for security reasons.Please contact us for more information.";
        echo(json_encode($json));
        exit;
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