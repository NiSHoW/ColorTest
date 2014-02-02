<?php
/**
 * Description of Request
 *
 * @author Nisho
 */

class Request {   
    
    protected $uri = '';
    
    protected $protocol = 'REDIRECT_URL';
    
    protected $baseUrl = '';
    
    /**
     * Stores requested page.
     * 
     * @var array
     */
    protected $segments = array();

    /**
     * Get requested page and explode it to segments.
     * 
     * @param string $protocol
     */
    public function __construct($options = array()){   
        $this->setOptions($options);
    }

    public function setProtocol($protocol) {
        $this->protocol = $protocol;
    }

    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }
        
    /**
     * Set options
     * @param array $options
     */
    public function setOptions(array $options){
        foreach($options as $name => $value){
            $method = 'set'.ucfirst($name);
            if(method_exists($this, $method )){
                $this->$method($value);
            }
        }        
    }
    
    
    public function parseRequest(){       
        $this->uri = (isset($_SERVER[$this->protocol])) ? $_SERVER[$this->protocol] : '';
        $this->segments = explode('/', rtrim($this->uri, '/'));
        array_shift($this->segments);
        
        $basePath = explode('/', rtrim($this->baseUrl, '/'));       
        foreach($basePath as $path){
            if(count($this->segments)){
                if($this->segments[0] && $this->segments[0] == $this->baseUrl)
                    array_shift($this->segments);
            }
        }
    }
    

    /**
     * Return all segments.
     * 
     * @return array
     */
    public function getAll(){   
        return $this->segments;
    }

    /**
     * Get requested controller.
     * 
     * @return mixed
     */
    public function getController(){
        if(isset($this->segments[0])){
            return strtolower($this->segments[0]);
        }else{
            return false;
        }
    }

    /**
     * Get requested action.
     * 
     * @return mixed
     */
    public function getAction(){
        if(isset($this->segments[1])){
            return strtolower($this->segments[1]);
        }else{
            return false;
        }
    }

    /**
     * Get requested parameters.
     * 
     * @return mixed
     */
    public function getParams(){
        if(isset($this->segments[2])){
            return array_slice($this->segments, 2);
        }else{
            return false;
        }
    }
}