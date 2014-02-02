<?php

include_once APPLICATION_LIBS.'/Request.php';
include_once APPLICATION_LIBS.'/TemplateManager.php';

abstract class Controller {
    
    protected $request;

    protected $template;
    
    public function __construct(Request $request) {        
        $this->request = $request;
        $this->template = TemplateManager::getInstance();        
    }
    
        
    public static function isAjaxRequest(){
        return isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    
    public function createPath($controller, $action, $params = array()){
        $basePath = $this->request->getBaseUrl();
        $basePath = ($basePath != '') ? $basePath.'/': '';
        $controller = $controller.'/';        
        $action = (($action != '') ? $action.'/' : '' );
        return '/'.$basePath.$controller.$action.http_build_query($params);        
    }
    
    
    public function redirect($path, $external = false){
        if(!$external){
            $basePath = $this->request->getBaseUrl();
            $basePath = ($basePath != '') ? '/'.$basePath: '';
        }
        header("location: ".$basePath.$path);
        exit;
    }
    
    
    public function setSession($session){
        if(isset($_SESSION['sessionName'])){
            $_SESSION[$_SESSION['sessionName']] = $session;        
        }
    }
    
    
    public function getSession(){
        if(isset($_SESSION['sessionName'])){
            if(isset($_SESSION[$_SESSION['sessionName']])){
                return $_SESSION[$_SESSION['sessionName']];
            }
        }
        return null;
    }
    
}