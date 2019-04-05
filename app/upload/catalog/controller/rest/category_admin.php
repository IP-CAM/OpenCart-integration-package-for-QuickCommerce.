<?php
/**
 * category_admin.php
 *
 * Category management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    1.0
 * @link       http://opencart-api.com/product/opencart-rest-admin-api/
 * @see        http://webshop.opencart-api.com/rest-admin-api/
 */
class ControllerRestCategoryAdmin extends Controller {

    private static $defaultFields = array(
        "category_description",
        "path",
        "parent_id",
        "category_store",
        "keyword",
        "top",
        "column",
        "sort_order",
        "status",
        "category_layout",
    );

    private static $defaultFieldValues = array(
        "category_description"=>array(),
        "category_layout"=>array(),
        "parent_id"=>0,
        "category_store"=>array(0),
        "top"=>0,
        "column"=>1,
        "sort_order"=>0,
        "status"=>1,
    );

    /*
   * Get categories list
   */
    public function listCategories($parent,$level) {

        $json['success']	= true;

        $this->load->model('catalog/category');

        $data = $this->loadCatTree($parent, $level);

        if(count($data) == 0){
            $json['success'] 	= false;
            $json['error'] 		= "No category found";
        }else {
            $json['data'] = $data;
        }

        $this->response->setOutput(json_encode($json));
    }

    /*
    * Get category details
    */
    public function getCategory($id) {

        $json = array('success' => true);

        $this->load->model('rest/restadmin');
        $this->load->model('tool/image');

        if (ctype_digit($id)) {
            $category_id = $id;
        } else {
            $category_id = 0;
        }

        $results = $this->model_rest_restadmin->getCategory($category_id);
        if(count($results)){
            $json['success']	= true;

            foreach ($results as $result) {
                if (isset($result['image']) && file_exists(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                } else {
                    $image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                }

                $languageId = isset($result['language_id']) ? $result['language_id'] : (int)$this->config->get('config_language_id');
                $json['data']['categories'][$result['category_id']][] = array(
                    'category_id'      => $result['category_id'],
                    'name'             => $result['name'],
                    'description'      => $result['description'],
                    'sort_order'       => $result['sort_order'],
                    'meta_title'       => $result['meta_title'],
                    'meta_description' => $result['meta_description'],
                    'meta_keyword'     => $result['meta_keyword'],
                    'language_id'      => $languageId,
                    'image'         => $image
                );

            }
        }else {
            $json['success']     = false;
            $json['error']       = "The specified category does not exist.";

        }

        $this->response->setOutput(json_encode($json));
    }

    public function loadCatTree($parent = 0, $level = 1) {

        $this->load->model('rest/restadmin');
        $this->load->model('tool/image');

        $result = array();

        $categories = $this->model_rest_restadmin->getCategories($parent);

        if ($categories && $level > 0) {
            $level--;

            foreach ($categories as $category) {
                if (isset($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
                    $image = $this->model_tool_image->resize($category['image'], $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                } else {
                    $image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('restadmin_thumb_width'), $this->config->get('restadmin_thumb_height'));
                }

                $languageId = isset($category['language_id']) ? $category['language_id'] : (int)$this->config->get('config_language_id');
                $result['categories'][$category['category_id']][] = array(
                    'category_id'      => $category['category_id'],
                    'name'             => $category['name'],
                    'description'      => $category['description'],
                    'sort_order'       => $category['sort_order'],
                    'meta_title'       => $category['meta_title'],
                    'meta_description' => $category['meta_description'],
                    'meta_keyword'     => $category['meta_keyword'],
                    'language_id'      => $languageId,
                    'image'         => $image,
                    'categories'    => $this->loadCatTree($category['category_id'], $level)
                );

            }

            return $result;
        }
    }

    /*
    * delete category
    {
        "categories": [8, 9 ]
    }
    */
    public function deleteCategory($post) {

        $json = array('success' => true);

        $this->load->language('catalog/category');
        $this->load->model('rest/restadmin');

        if (isset($post['categories'])) {
            foreach ($post['categories'] as $category_id) {
                $this->model_rest_restadmin->deleteCategory($category_id);
            }
        } else {
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    public function addCategory($post) {

        $json = array('success' => true);

        $this->load->language('catalog/category');
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
            $category_id = $this->model_rest_restadmin->addCategory($post);

            $json["data"]["id"] = $category_id;
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }


    public function editCategory($id, $post) {

        $json = array('success' => true);

        $this->load->language('catalog/category');
        $this->load->model('rest/restadmin');

        $error = $this->validateForm($post, $id);

        $data = $this->model_rest_restadmin->getCategory($id);

        $this->loadData($post, $data);

        if (!empty($post) && empty($error)) {
             $this->model_rest_restadmin->editCategory($id, $post);
        } else {
            $json['error'] = $error;
            $json["success"] = false;
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm($post, $category_id = null) {

        $error  = array();

        if(isset($post['category_description'])) {
            foreach ($post['category_description'] as $category_description) {
                if ((utf8_strlen($category_description['name']) < 2) || (utf8_strlen($category_description['name']) > 255)) {
                    $error['name'][$category_description['language_id']] = $this->language->get('error_name');
                }

                if ((utf8_strlen($category_description['meta_title']) < 3) || (utf8_strlen($category_description['meta_title']) > 255)) {
                    $error['meta_title'][$category_description['language_id']] = $this->language->get('error_meta_title');
                }
            }
        }

        if(isset($post['keyword'])) {
            if (utf8_strlen($post['keyword']) > 0) {
                $this->load->model('catalog/url_alias');

                $url_alias_info = $this->model_catalog_url_alias->getUrlAlias($post['keyword']);

                if ($url_alias_info && isset($category_id) && $url_alias_info['query'] != 'category_id=' . $category_id) {
                    $error['keyword'] = sprintf($this->language->get('error_keyword'));
                }

                if ($url_alias_info && !isset($category_id)) {
                    $error['keyword'] = sprintf($this->language->get('error_keyword'));
                }

            }
        }

        return $error;
    }

    /*
    * Add category
     *
      {
            "sort_order": 1,
            "parent_id": 1,
            "top": 1,
            "column": 1,
            "category_store": 1,
            "status": 1,
            "category_description": [
                {
                    "language_id": 1,
                    "name": "SUPER MEGA GIGA demo demo",
                    "description": "desc",
                    "meta_title": "meta_title",
                    "meta_description": "meta_description",
                    "meta_keyword": "meta_keyword"
                }
            ]
      }
    */

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
    
    /*CATEGORY FUNCTIONS
    * index.php?route=rest/category_admin/category
    */
    public function category() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get category details
            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getCategory($this->request->get['id']);
            }else {
                /*check parent parameter*/
                if (isset($this->request->get['parent'])) {
                    $parent = $this->request->get['parent'];
                } else {
                    $parent = 0;
                }

                /*check level parameter*/
                if (isset($this->request->get['level'])) {
                    $level = $this->request->get['level'];
                } else {
                    $level = 1;
                }

                $this->listCategories($parent, $level);
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson)) {
                $this->addCategory($requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
                && !empty($requestjson)) {
                $this->editCategory($this->request->get['id'], $requestjson);
            }else {
                $this->response->setOutput(json_encode(array('success' => false)));
            }
        } else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
            $requestjson = file_get_contents('php://input');
            $requestjson = json_decode($requestjson, true);

            if (!empty($requestjson) && isset($requestjson["categories"])) {
                $this->deleteCategory($requestjson);
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

    /*
    * CATEGORY IMAGE MANAGEMENT FUNCTIONS
    */
    public function categoryimages() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //upload and save image
            $this->saveCategoryImage($this->request);
        }
    }

    /*
    * Upload and save category image
    */
    public function saveCategoryImage($request) {
        $json = array('success' => false);

        $this->load->model('catalog/category');
        $this->load->model('rest/restadmin');

        if (ctype_digit($request->get['id'])) {
            $category = $this->model_catalog_category->getCategory($request->get['id']);
            //check category exists
            if(!empty($category)) {
                if(isset($request->files['file'])){
                    $uploadResult = $this->upload($request->files['file'], "categories");
                    if(!isset($uploadResult['error'])){
                        $json['success']     = true;
                        $this->model_rest_restadmin->setCategoryImage($request->get['id'], $uploadResult['file_path']);
                    }else{
                        $json['error']	= $uploadResult['error'];
                    }
                } else {
                    $json['error']	= "File is required!";
                }
            }else {
                $json['success']	= false;
                $json['error']      = "The specified category does not exist.";
            }
        } else {
            $json['success']    = false;
        }

        $this->response->setOutput(json_encode($json));
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
