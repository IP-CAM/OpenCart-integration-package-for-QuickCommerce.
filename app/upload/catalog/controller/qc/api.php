<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Doctrine\Common\Collections\Collection;

class ControllerQCApi extends Controller {
    /**
     * @var
     */
    protected $settings;
    /**
     * @var EntityManager
     */
    public $em;
    /**
     * @var string
     */

    public function getService() {

    }

    // These have also been included via mod in the base controller class
    protected function getPostVar($key, $default = null) {
        return $this->getRequestVar($key, 'post', $default);
    }

    protected function getRequestVar($key, $type = 'get', $default = null) {
        $types = array('get', 'post');
        if (!in_array($type, $types)) {
            throw new Exception('Invalid request type');
        }

        if (isset($this->request->{$type}[$key])) {
            if (!empty($this->request->{$type}[$key])) {
                return $this->request->{$type}[$key];
            }
        }

        return $default;
    }

    /**
     * Returns the mappings for converting an OpenCart doctrine entity to a QuickBooks (or another remote) entity.
     * To convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents try mapDoctrineEntity.
     *
     * @param $entityName
     * @param $export Setting this flag to true will flip the mappings
     * @return mixed
     */
    protected function getMappings($entityName, $export = false) {
        if (!isset($this->mappings[$entityName])) {
            $this->buildMappings($entityName, $export);
        }

        return $this->mappings[$entityName];
    }

    /**
     * Builds mappings used for converting an OpenCart doctrine entity to a QuickBooks entity.
     *
     * @param null $entityName
     * @param bool|false $export
     */
    protected function buildMappings($entityName = null, $export = false) {
        $entityName = ($entityName != null) ? $entityName : $this->foreign;
        EntityMapper::mapEntities($this->em, $entityName, $this->mapXml, $this->mappings, $export);
    }

    /**
     * @param $registry
     * @throws Exception
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    function __construct($registry) {
        /*if (empty($this->tableName)) // TODO: Interface yo
            throw new Exception('Mapping table name ($tableName) was not specified in the extending controller class');
        if (empty($this->joinTableName)) // TODO: Interface yo
            throw new Exception('Join table name ($joinTableName) was not specified in the extending controller class');
        if (empty($this->joinCol)) // TODO: Interface yo
            throw new Exception('Join column name ($joinCol) was not specified in the extending controller class');*/

        parent::__construct($registry);

        $di = new DoctrineInitializer($this, $registry);
    }

    public function checkPlugin() {
        $this->config->set('config_error_display', 0);

        $this->response->addHeader('Content-Type: application/json');

        $json = array("success"=>false);

        /*check rest api is enabled*/
        /*if (!$this->config->get('restadmin_status')) {
            $json["error"] = 'Rest Admin API is disabled. Enable it!';
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

    public function get($args) {
        //var_dump($args);
        $resource = \App\Resource::load($args['resource']);
        //var_dump($resource);

        $resource->setEntityManager($this->em);
        $resource->init();
        if ($resource === null) {
            \App\Resource::response($this->request, $this->response, \App\Resource::STATUS_NOT_FOUND);
        } else {
            if (!empty($args['id'])) {
                //$this->response->addHeader('Content-Disposition: attachment; filename="' . implode('_', [$args['resource'], $args['id']]) . '.json"');
                $resource->get($this->request, $this->response, $args['id']);
            } else {
                //$this->response->addHeader('Content-Disposition: attachment; filename="' . $args['resource'] . '.json"');
                $resource->get($this->request, $this->response, null);
            }
        }
    }

    public function put($args) {
        $resource = \App\Resource::load($args['resource']);
        if ($resource === null) {
            \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
        } else {
            $resource->put($args['id']);
        }
    }

    public function post($args) {
        $resource = \App\Resource::load($args['resource']);
        if ($resource === null) {
            \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
        } else {
            $resource->post();
        }
    }

    public function delete($args) {
        $resource = \App\Resource::load($args['resource']);
        if ($resource === null) {
            \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
        } else {
            $resource->delete($args['id']);
        }
    }

    public function index() {
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/qcapi/{resource}[/{id:\d+}]', array(__CLASS__, 'get'));
            $r->addRoute('POST', '/qcapi/{resource}[/{id:\d+}]', array(__CLASS__, 'post'));
            $r->addRoute('PUT', '/qcapi/{resource}[/{id:\d+}]', array(__CLASS__, 'put'));
            $r->addRoute('DELETE', '/qcapi/{resource}[/{id:\d+}]', array(__CLASS__, 'delete'));

        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                echo 'Route not found';
                exit;
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                call_user_func($handler, $vars);
                break;
        }

        // Get
        /*$app->get('/api/{resource}[/{id:\d+}]', function($req, $res, $args) {
            $resource = \App\Resource::load($args['resource']);

            $resource->setEntityManager($this->getContainer()->get('EntityManager'));
            $resource->init();



            if ($resource === null) {
                \App\Resource::response($req, $res, \App\Resource::STATUS_NOT_FOUND);
            } else {
                if (!empty($args['id'])) {
                    $resource->get($req, $res, $args['id']);
                } else {
                    $resource->get($req, $res, null);
                }
            }
        });

        // Post
        $app->post('/api/{resource}[/{id:\d+}]', function($req, $res, $args) {
            $resource = \App\Resource::load($args['resource']);
            if ($resource === null) {
                \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
            } else {
                $resource->post();
            }
        });

        // Put
        $app->put('/api/{resource}[/{id:\d+}]', function($req, $res, $args) {
            $resource = \App\Resource::load($args['resource']);
            if ($resource === null) {
                \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
            } else {
                $resource->put($args['id']);
            }
        });

        // Delete
        $app->delete('/api/{resource}[/{id:\d+}]', function($req, $res, $args) {
            $resource = \App\Resource::load($args['resource']);
            if ($resource === null) {
                \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
            } else {
                $resource->delete($args['id']);
            }
        });

        // Options
        $app->options('/api/{resource}[/{id:\d+}]', function($req, $res, $args) {
            $resource = \App\Resource::load($args['resource']);
            if ($resource === null) {
                \App\Resource::response(\App\Resource::STATUS_NOT_FOUND);
            } else {
                $resource->options();
            }
        });*/

        //$this->sendResponse('word up');
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