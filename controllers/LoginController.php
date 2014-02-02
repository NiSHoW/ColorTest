<?php

include_once APPLICATION_LIBS.'/Controller.php';

class LoginController extends Controller{
        
    
    public function indexAction(){        
        if(Controller::isAjaxRequest()){
            return $this->template->dispatchPartial('login');
        }         
        return $this->template->dispatch('login');
    }
    
    
    public function submitAction(){

        $this->template->params = $_GET;

        if($_GET['sessionName'] !== ''){
            $_SESSION['sessionName'] = $_GET['session'];
            $this->setSession(array());
            $this->redirect('/');
        }
        
        if(Controller::isAjaxRequest()){
            return $this->template->dispatchPartial('login');
        }         
        return $this->template->dispatch('login');
    }    
    
    
    
    public function logoutAction(){
        if(!isset($_SESSION['sessionName'])){
            $this->redirect('/');
        }
        
        session_destroy();
        return $this->template->dispatch('logout');
    }  
    
}