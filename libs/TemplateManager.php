<?php

class TemplateManager {
    
    private static $instance = null;
    
    protected $templateFolder;
       
    protected $mainTemplateFile;
    
    protected $variables = array();
    
    protected $sections;
    
    protected $basePath;

    protected function __construct($options = array()) {        
        $this->basePath = '/';
        $this->sections = new stdClass();
        $this->templateFolder = dirname(__FILE__).'/templates';        
        $this->mainTemplateFile = 'main';
        $this->setOptions($options);
    }
    
    public static function init($options = array()){
        TemplateManager::$instance = new TemplateManager($options);
    }
    
    
    public static function getInstance(){
        if(!TemplateManager::$instance instanceof TemplateManager){
            throw new Exception("Call init function befoure use it");
        }
        
        return TemplateManager::$instance;
    }
    
    
    public function setOptions($options = array()){
        foreach($options as $name => $value){
            $method = 'set'.ucfirst($name);
            if(method_exists($this, $method )){
                $this->$method($value);
            }
        }        
    }
    
    
    public function __set($name, $value) {
        $this->variables[$name] = $value;
    }
    
    public function __get($name) {
        if(isset($this->variables[$name])){
            return $this->variables[$name];
        }        
        return null;
    }


    public function dispatch($template){        
        //call partial dispatch
        $this->sections->body = $this->dispatchPartial($template);       

        ob_start();
        include $this->templateFolder .'/'. $this->mainTemplateFile.'.tpl.php';
        return ob_get_clean();                
    }
    
    
    public function dispatchPartial($template){      
        ob_start();
        include $this->templateFolder .'/'. $template.'.tpl.php';
        return ob_get_clean();
    }
    
    /**
     * Set template folder
     * @param type $templateFolder
     */
    public function setTemplateFolder($templateFolder){
        $this->templateFolder = $templateFolder;
    }   
    
    /**
     * Set template folder
     * @param type $templateFolder
     */
    public function setBasePath($path){
        $this->basePath = $path;
    }   
    
}

