<?php

namespace Orchestra\routing;

/**
 * Class that handles Route Collection
 * 
 * (c) @author
 * 
 * @author Creator-Solutions Owen Burns
 * @author Founder-Studios Owen Burns
 */
class Router{

    /**
     * @var array
     */
    protected array $routes = array();

    public function add($alias, array $options){        
        if (array_key_exists($alias, $this->routes)){
            $this->routes[$alias][] = $options;
        }else{
            $this->routes[$alias] = array($options);  
        }
    }

    public function getAll():array{
        return $this->routes;
    }    
}