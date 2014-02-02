<?php

include_once APPLICATION_LIBS.'/Controller.php';

class DefaultController extends Controller{
            
    public function indexAction(){        
        
        $session = $this->getSession();

        if(isset($session['TestWS'])){
            $this->template->statusTestWS = $session['TestWS']['status'];            
        }
        
        if(isset($session['TestL'])){ 
            $this->template->statusTestL = $session['TestL']['status'];
        }        
        
        if(Controller::isAjaxRequest()){
            return $this->template->dispatchPartial('select-test');
        }         
        return $this->template->dispatch('select-test');
    }  
    
    
    public function pingAction(){
        if(!$this->isAjaxRequest()){        
            $this->redirect("/error");            
        }
                
        $status = false;
        if($this->getSession() !== null){
            $status = true;
        }
        
        echo json_encode(array('status' => $status));
        exit();
    }
    
}