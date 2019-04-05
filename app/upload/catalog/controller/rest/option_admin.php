<?php
/**
 * option_admin.php
 *
 * Option management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestOptionAdmin extends Controller {

    /*
    * Get options
    */
    public function listOption($request) {

        $json = array('success' => false);

        $this->load->language('catalog/option');
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

        $options = array();

        $results = $this->model_rest_restadmin->getOptions($parameters);

        foreach ($results as $result) {
            $info = array(
                'option_id'  => $result['option_id'],
                'name'       => $result['name'],
                'sort_order' => $result['sort_order'],
            );

            $option_values = $this->model_rest_restadmin->getOptionValueDescriptions($result['option_id']);

            $this->load->model('tool/image');

            $info['option_values'] = array();

            foreach ($option_values as $option_value) {
                if (is_file(DIR_IMAGE . $option_value['image'])) {
                    $image = $option_value['image'];
                    $thumb = $option_value['image'];
                } else {
                    $image = '';
                    $thumb = 'no_image.png';
                }

                $info['option_values'][] = array(
                    'option_value_id'          => $option_value['option_value_id'],
                    'option_value_description' => $option_value['option_value_description'],
                    'image'                    => $image,
                    'thumb'                    => $this->model_tool_image->resize($thumb, $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height')),
                    'sort_order'               => $option_value['sort_order']
                );
            }

            $options['options'][] = $info;
        }

        if (count($options) == 0 || empty($options)) {
            $json['error'] = "No options found";
        } else {
            $json['success'] = true;
            $json['data'] = $options['options'];
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
    * delete options
    {
        "options": [8, 9 ]
    }
    */
    public function deleteOption($post) {

        $json = array('success' => true);

        $this->load->language('catalog/option');
        $this->load->model('rest/restadmin');

        $error = $this->validateDelete($post);

        if (isset($post['options']) && empty($error)) {
            foreach ($post['options'] as $option_id) {
                $this->model_rest_restadmin->deleteOption($option_id);
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
    * Add option
     *
      {
            "sort_order": 1,
            "type": "radio",
            "option_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA demo demo"
                }
            ],
            "option_value":[
                {
                    image": "",
                    "sort_order": 1,
                    "option_value_description": [
                        {
                            "language_id": 1,
                            "name": "SUPER MEGA GIGA option 1"
                        }
                    ]
                },
                {
                    image": "",
                    "sort_order": 1,
                    "option_value_description": [
                        {
                            "language_id": 1,
                            "name": "SUPER MEGA GIGA option 2"
                        }
                    ]
                }
            ]
      }
    */
    public function addOption($post) {

        $json = array('success' => true);

        $this->load->language('catalog/option');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
            $retval = $this->model_rest_restadmin->addOption($post);
	    $json["data"]["id"] = $retval;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Edit option
     *
      {
            "sort_order": 1,
            "type": "radio",
            "option_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA demo demo"
                }
            ],
            "option_value":[
                {
                    "image": "",
                    "sort_order": 1,
                    "option_value_id": 57,
                    "option_value_description": [
                        {
                            "language_id": 1,
                            "name": "SUPER MEGA GIGA option 1 mod"
                        }
                    ]
                },
                {
                    "image": "",
                    "sort_order": 1,
                    "option_value_id": 58,
                    "option_value_description": [
                        {
                            "language_id": 1,
                            "name": "SUPER MEGA GIGA option 2 mod"
                        }
                    ]
                }
            ]
      }
    */
    public function editOption($id, $post) {

        $json = array('success' => true);

        $this->load->language('catalog/option');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editOption($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post) {

        $error  = array();

        foreach ($post['option_description'] as $option_description) {
            if ((utf8_strlen($option_description['name']) < 1) || (utf8_strlen($option_description['name']) > 128)) {
                $error['name'][$option_description['language_id']] = $this->language->get('error_name');
            }
        }

        if (($post['type'] == 'select' || $post['type'] == 'radio' || $post['type'] == 'checkbox') && !isset($post['option_value'])) {
            $error['warning'] = $this->language->get('error_type');
        }

        if (isset($post['option_value'])) {
            foreach ($post['option_value'] as $option_value_id => $option_value) {
                foreach ($option_value['option_value_description'] as $option_value_description) {
                    if ((utf8_strlen($option_value_description['name']) < 1) || (utf8_strlen($option_value_description['name']) > 128)) {
                        $error['option_value'][$option_value_id][$option_value_description["language_id"]] = $this->language->get('error_option_value');
                    }
                }
            }
        }

        return $error;
    }

    protected function validateDelete($post) {

        $this->load->model('rest/restadmin');

        $error  = array();

        foreach ($post['options'] as $option_id) {
            $product_total = $this->model_rest_restadmin->getTotalProductsByOptionId($option_id);

            if ($product_total) {
                $error['warning'] = sprintf($this->language->get('error_product'), $product_total);
            }
        }

        return $error;
    }

    /*
    * OPTION IMAGE MANAGEMENT FUNCTIONS
    */
    public function optionimages() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //upload and save image
            $this->saveOptionImage($this->request);
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson) && isset($requestjson["image"])) {
                $this->updateOptionValueImage($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        }
    }

    /*
    * Upload and save option image
    */
    public function saveOptionImage($request) {
        $json = array('success' => false);

        $this->load->model('rest/restadmin');

        if (ctype_digit($request->get['id'])) {
            $option = $this->model_rest_restadmin->getOptionValue($request->get['id']);
            if(!empty($option)) {
                if(isset($request->files['file'])){
                    $uploadResult = $this->upload($request->files['file'], "product_options");
                    if(!isset($uploadResult['error'])){
                        $json['success']     = true;
                        $this->model_rest_restadmin->setOptionImage($request->get['id'], $uploadResult['file_path']);
                    }else{
                        $json['error']	= $uploadResult['error'];
                    }
                } else {
                    $json['error']	= "File is required!";
                }
            }else {
                $json['success']	= false;
                $json['error']      = "The specified option value does not exist.";
            }
        } else {
            $json['success']    = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Update option image path
    */
    public function updateOptionValueImage($id, $request) {
        $json = array('success' => false);

        $this->load->model('rest/restadmin');

        $option = $this->model_rest_restadmin->getOptionValue($id);

        if(!empty($option)) {
            $json['success']     = true;
            $this->model_rest_restadmin->setOptionImage($id, $request['image']);
        }else {
            $json['success']	= false;
            $json['error']      = "The specified option value does not exist.";
        }

        $this->response->setOutput(json_encode($json));
    }


    /*
    * OPTION FUNCTIONS
    * index.php?route=rest/option/option
    */
    public function option() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            $this->listOption($this->request);
        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addOption($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editOption($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["options"])) {
                $this->deleteOption($requestjson);
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
