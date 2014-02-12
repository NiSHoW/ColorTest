<?php

include_once APPLICATION_LIBS.'/Controller.php';

class DefaultController extends Controller{
            
    public function indexAction(){        
        
        $session = $this->getSession();

        if(isset($session['TestWS']) && $session['TestWS']['status'] == 'complete'){
            $this->template->statusTestWS = $session['TestWS']['status'];   
            $this->template->outputTestWS = $session['TestWS']['output'];   
        }
        
        if(isset($session['TestL']) && $session['TestL']['status'] == 'complete'){ 
            $this->template->statusTestL = $session['TestL']['status'];
            $this->template->outputTestL = $session['TestL']['output'];
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
        $session = $this->getSession();
        if($this->getSession() !== null){
            $session['lastAccess'] = time();
            $this->setSession($session);
            $status = true;
        }
        
        echo json_encode(array('status' => $status, 'lastTime' => date('Y-m-d H:i', $session['lastAccess'])));
        exit();
    }
    
}