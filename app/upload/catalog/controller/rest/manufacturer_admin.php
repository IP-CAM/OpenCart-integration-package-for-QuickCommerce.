<?php
/**
 * manufacturer_admin.php
 *
 * Manufacturer management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestManufacturerAdmin extends Controller {

    private static $defaultFields = array(
        "name",
        "manufacturer_store",
        "keyword",
        "sort_order"
    );

    private static $defaultFieldValues = array(
        "manufacturer_store"=>array(0)
    );

    /*
    * Get manufacturers
    */
    public function listManufacturer($request) {

        $json = array('success' => false);

        $this->load->language('restapi/manufacturer');
        $this->load->model('rest/restadmin');
        $this->load->model('tool/image');

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

        $manufacturers = array();

        $results = $this->model_rest_restadmin->getManufacturers($parameters);

        foreach ($results as $result) {
            if (isset($result['image']) && file_exists(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
            }

            $manufacturers['manufacturers'][] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'name'            => $result['name'],
                'sort_order'      => $result['sort_order'],
                'image'			  => $image
            );
        }

        if (count($manufacturers) == 0 || empty($manufacturers)) {
            $json['error'] = "No manufacturers found";
        } else {
            $json['success'] = true;
            $json['data'] = $manufacturers['manufacturers'];
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * delete manufacturer
    {
        "manufacturers": [8, 9 ]
    }
    */
    public function deleteManufacturer($post) {

        $json = array('success' => true);

        $this->load->language('restapi/manufacturer');
        $this->load->model('rest/restadmin');

        $error = $this->validateDelete($post);

        if (isset($post['manufacturers']) && empty($error)) {
            foreach ($post['manufacturers'] as $manufacturer_id) {
                $this->model_rest_restadmin->deleteManufacturer($manufacturer_id);
            }
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Add manufacturer
     *
      {
            "sort_order": 1,
            "name":"demo manufacturer",
            "image":"image url",
            "keyword":"keyword",
            "manufacturer_store": [0]
      }
    */
    public function addManufacturer($post) {

        $json = array('success' => true);

        $this->load->language('restapi/manufacturer');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {

            $this->loadData($post);

            $retval  =$this->model_rest_restadmin->addManufacturer($post);
            $json["data"]["id"] = $retval;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Edit manufacturer
     *
      {
            "sort_order": 1,
            "name":"demo manufacturer",
            "image":"image url",
            "keyword":"keyword",
            "manufacturer_store": [0]
      }
    */
    public function editManufacturer($id, $post) {

        $json = array('success' => true);

        $this->load->language('restapi/manufacturer');
        $this->load->model('rest/restadmin');

        $data = $this->model_rest_restadmin->getManufacturer($id);

        $this->loadData($post, $data);

        $error = $this->validateForm($post, $id);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editManufacturer($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post, $manufacturer_id = null) {

        $error  = array();

        if ((utf8_strlen($post['name']) < 2) || (utf8_strlen($post['name']) > 64)) {
            $error['name'] = $this->language->get('error_name');
        }

        if (utf8_strlen($post['keyword']) > 0) {
            $this->load->model('rest/restadmin');

            $url_alias_info = $this->model_rest_restadmin->getUrlAlias($post['keyword']);

            if ($url_alias_info && !empty($manufacturer_id) && $url_alias_info['query'] != 'manufacturer_id=' . $manufacturer_id) {
                $error['keyword'] = sprintf($this->language->get('error_keyword'));
            }

            if ($url_alias_info && empty($manufacturer_id)) {
                $error['keyword'] = sprintf($this->language->get('error_keyword'));
            }
        }

        return $error;
    }

    protected function validateDelete($post) {

        $this->load->model('rest/restadmin');

        $error  = array();

        foreach ($post['manufacturers'] as $manufacturer_id) {
            $product_total = $this->model_rest_restadmin->getTotalProductsByManufacturerId($manufacturer_id);

            if ($product_total) {
                $error['warning'] = sprintf($this->language->get('error_product'), $product_total);
            }
        }

        return $error;
    }

    /*
    * MANUFACTURER FUNCTIONS
    * index.php?route=rest/manufacturer_admin/manufacturer
    */
    public function manufacturer() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listManufacturer($this->request);

        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addManufacturer($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editManufacturer($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["manufacturers"])) {
                $this->deleteManufacturer($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }


    /*
    * MANUFACTURER IMAGE MANAGEMENT FUNCTIONS
    */
    public function manufacturerimages() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //upload and save manufacturer image
            $this->saveManufacturerImage($this->request);
        }
    }


    /*
    * Upload and save manufacturer image
    */
    public function saveManufacturerImage($request) {

        $json = array('success' => false);

        $this->load->model('catalog/manufacturer');
        $this->load->model('rest/restadmin');

        if (ctype_digit($request->get['id'])) {
            $manufacturer = $this->model_catalog_manufacturer->getManufacturer($request->get['id']);
            //check manufacturer exists
            if(!empty($manufacturer)) {
                if(isset($request->files['file'])){
                    $uploadResult = $this->upload($request->files['file'], "manufacturers");
                    if(!isset($uploadResult['error'])){
                        $json['success']     = true;
                        $this->model_rest_restadmin->setManufacturerImage($request->get['id'], $uploadResult['file_path']);
                    }else{
                        $json['error']	= $uploadResult['error'];
                    }
                } else {
                    $json['error']	= "File is required!";
                }
            }else {
                $json['success']	= false;
                $json['error']      = "The specified manufacturer does not exist.";
            }
        } else {
            $json['success']    = false;
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

    private function loadData(&$data, $item=null) {
        foreach(self::$defaultFields as $field){
            if(!isset($data[$field])){
                if(!empty($item) && isset($item[$field])){
                    $data[$field] = $item[$field];
                } else {
                    if(!isset(self::$defaultFieldValues[$field])){
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
?>
