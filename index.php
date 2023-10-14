<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

if (! defined('PHPNEXUS_VERSION')) {
    require_once 'autoload.php';
}

include_once(__DIR__ .'/core/config/routes.php');

use Orchestra\http\UrlMatcher;
use Orchestra\bandwidth\TokenBucket;
use Orchestra\bandwidth\Rate;
use Orchestra\bandwidth\BlockingConsumer;
use Orchestra\bandwidth\storage\FileStorage;
use Orchestra\bandwidth\storage\SessionStorage;
use Orchestra\io\FileHandler;

/**
 * Main indexer file -> reads Url data
 * => points to correct controller::Action
 * 
 * (c) @author
 * 
 * @author Creator-Solutions Owen Burns
 * @author Founder-Studios Owen Burns
 */
class Index{

    /**
     * @var RouteCollection
     */
    private RouteCollection $routeCollection;

    /**
     * @var array
     */
    private array $routeCollections;

    /**
     * @var array
     */
    private array $url;

    /**
     * @var string
     */
    private string $uri;

    /**
     * @var UrlMatcher
     */
    private UrlMatcher $matcher;

    /**
     * @var array
     */
    private array $routeConfigs;

    

    public function __construct(){
        $this->routeCollection = new RouteCollection();
        $this->routeCollections = $this->routeCollection->getRouteCollection();

        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url = explode( '/', $this->uri);

        $this->matcher = new UrlMatcher($this->routeCollections);
        $this->routeConfigs = $this->matcher->serialize('/'.$this->url[2], $this->url[3]);

        session_start();
    }

    public function getRoutes(){
       print_r($this->routeConfigs);
    }

    public function execute(){
        $controllerName = $this->routeConfigs['_controller'];
        $callback = $this->routeConfigs['_callback'];

        $storage = new SessionStorage("Founders");
        $rate = new Rate(10, Rate::MINUTE);
        $bucket = new TokenBucket(10, $rate, $storage);
        $bucket->bootstrap(10);

        try{
            if ($bucket->consume(1)){
                $instance = new $controllerName();
                $result = $instance->$callback();
    
                echo $result;
            }else{
                http_response_code(429);
                echo json_encode(array(
                    'state' => false,
                    'message' => 'Rate limit exceeded'
                ));
            }        
        }catch (Exception $e){
            http_response_code(429);
            echo $e;
        }
    }
}

$index = new Index();
$index->execute();