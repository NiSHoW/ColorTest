<?php

/**
 * Description of TestWS
 *
 * @author Nisho
 */
include_once APPLICATION_LIBS.'/TestController.php';
        

class TestWSController extends TestController{

    
    public function indexAction(){
        if(!$this->checkSession()){
            //invio errore
            echo "ERRORE";
            exit();
        }
        
        $session = $this->getSession();
        $this->template->colors = $session['TestWS']['colors'];
        $this->template->angle = $session['TestWS']['angle'];
        $this->template->infoEnabled = true;
        
        if(Controller::isAjaxRequest()){
            return $this->template->dispatchPartial('test/testws');
        }         
        return $this->template->dispatch('test/testws');
        
    }    
    
    public function saveAction(){
        $this->checkSession();
        if(!Controller::isAjaxRequest()){
            return $this->redirect('/');
        }
        
        $session = $this->getSession();
        $session['TestWS']['status'] = 'complete';
        $session['TestWS']['endTime'] = time();
        $this->setSession($session);
        
        $colors = json_decode($_POST['colors']);        
        $this->exportToFile($colors);
        
        $basePath = $this->request->getBaseUrl();
        $basePath = ($basePath != '') ? '/'.$basePath: '';
        
        echo json_encode(array(
            'status' => 'OK',
            'redirect' => $basePath.'/'
        ));
        exit();
    }    

    
    /**
     * Check session
     * @return boolean
     */
    protected function checkSession(){
        $session = $this->getSession();        
        if(!isset($session['TestWS'])){
            $session = $this->initialieSession();
        }
        
        if(isset($session['TestWS']['filename'])){
            $this->filename = $session['TestWS']['filename'];
        }
        
        if($session['TestWS']['status'] == 'complete'){
            return false;    
        }
        
        return true;
    }
    
    
    /**
     * Initialize Session
     * @return type
     */
    protected function initialieSession(){
        $session = $this->getSession();
        $session['TestWS'] = array(
            'status' => 'init',
            'startTime' => time(),
        ); 
                
        $colors = array();
        $colorrows = file(TESTWA_COLORI_RIFERIMENTO);
        
        foreach($colorrows as $i => $row){
            $lab = preg_split('/\s+/', $row);
            if(count($lab) >= 3){
                $c = $i % 3 ;
                if($c == 0){
                    $color = array();
                    if($i == 0){
                        $color['final'] = true;
                    }            
                    $color['max'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
                } elseif($c == 1){
                    $color['lab'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
                } else {
                    $color['min'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
                    $colors[] = $color;    
                }
            }    
        }
        
        $angles = array(36, 72, 252, 278);
        $session['TestWS']['initColors'] = $colors;
        $session['TestWS']['angle'] = $angles[rand(0, count($angles))];            
        
        while(!shuffle($colors)){};        
        $session['TestWS']['colors'] = $colors;
        
        $this->setSession($session);
        return $session;
    }
    
    
    
    protected function exportToFile($colors){
        
        $session = $this->getSession();
        
        $output = "";
        $output = "Sessione utente: ".$_SESSION['sessionName']."\r\n";
        $output = "inizio: ".date('d-m-y H:m:s', $session['TestWS']['startTime'])."\r\n";
        $output = "fine: ".date('d-m-y H:m:s', $session['TestWS']['endTime'])."\r\n";
        
        //output initcolors
        $output .= "\r\nColori iniziali letti da file:\r\n";
        foreach($session['TestWS']['initColors'] as $color){
            $output .= "\t".$color['max']['L']."\t".$color['max']['a']."\t".$color['max']['b']."\r\n";
            $output .= "\t".$color['lab']['L']."\t".$color['lab']['a']."\t".$color['lab']['b']."\r\n";
            $output .= "\t".$color['min']['L']."\t".$color['min']['a']."\t".$color['min']['b']."\r\n\r\n";
        }
        
        $output .= "\r\nDisposizione Casuale dei color:\r\n";
        foreach($colors as $color){
            $output .= "\t".$color->labMax->L."\t".$color->labMax->a."\t".$color->labMax->b."\r\n";
            $output .= "\t".$color->labMid->L."\t".$color->labMid->a."\t".$color->labMid->b."\r\n";
            $output .= "\t".$color->labMin->L."\t".$color->labMin->a."\t".$color->labMin->b."\r\n\r\n";
        }        
             
        $output .= "\r\nColori modificati dall'utente:\r\n";
        foreach($colors as $color){
            $output .= "\t".$color->lab->L."\t".$color->lab->a."\t".$color->lab->b."\r\n";
        }   
                
        $this->filename = APPLICATION_RESULTS.'/'.$_SESSION['sessionName'].
                date('d-m-y', $session['TestWS']['startTime']).'-WS.txt';
       
        $session['TestWS']['filename'] = $this->filename;
        $this->setSession($session);        
        
        file_put_contents($this->filename, $output);
        return $this->filename;
    }
    
}
