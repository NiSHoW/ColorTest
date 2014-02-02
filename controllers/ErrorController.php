<?php

include_once APPLICATION_LIBS.'/Controller.php';
/**
 * Description of ErrorController
 *
 * @author Nisho
 */
class ErrorController extends Controller{
            
    public function E404Action(){        
        if(Controller::isAjaxRequest()){
            return $this->template->dispatchPartial('errors/404');
        }         
        return $this->template->dispatch('errors/404');
    }

}