<?php
/**
 * Bootstrap Script
 */
define('APPLICATION_PATH', dirname(__FILE__));
define('APPLICATION_LIBS', APPLICATION_PATH.'/libs');
define('APPLICATION_CONTROLLERS', APPLICATION_PATH.'/controllers');
define('APPLICATION_TEMPLATES', APPLICATION_PATH.'/templates');
define('APPLICATION_RESULTS', dirname(__FILE__).'/results');

define('TESTWA_COLORI_RIFERIMENTO', APPLICATION_PATH.'/colori_riferimento.txt');
define('TESTL_COLORI_RIFERIMENTO', APPLICATION_PATH.'/colori_riferimento.txt');

//initialize session
session_start();

//controllo dei path
include_once APPLICATION_LIBS.'/Request.php';
$Request = new Request();
$Request->parseRequest();

//check status
include_once(APPLICATION_LIBS.'/TemplateManager.php');
$TemplateManager = TemplateManager::init(array(
    'templateFolder' => APPLICATION_TEMPLATES,
    'basePath' => '/'
));

//dispatch correct 
$controller = 'default';
$action = 'index';

if($Request->getController()){    
    $controller = $Request->getController();
}
if($Request->getAction()){    
    $action = $Request->getAction();  
}
//CHECK SESSION
if(!isset($_SESSION['sessionName']) &&
   $controller != 'login'){
    $controller = 'login';
    $action = 'index';    
}

//GENERATE CONTROLLER
$controllerName = ucfirst($controller)."Controller";
if(file_exists(APPLICATION_CONTROLLERS."/".$controllerName.'.php')){
    include_once APPLICATION_CONTROLLERS."/".$controllerName.'.php';     
}

try{
    if(class_exists($controllerName)){
        $controller = new $controllerName($Request);
    } else {
        throw new Exception("Controller not found");
    }

    $actionName = $action."Action";
    if(method_exists($controller, $actionName)){    
        echo $controller->$actionName();
    } else {
        throw new Exception("Action not found");
    }
    
} catch (Exception $ex){
    
    include_once APPLICATION_CONTROLLERS."/ErrorController.php";
    $controller = new ErrorController($Request);
    echo $controller->E404Action();
}
