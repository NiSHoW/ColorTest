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
        $this->template->calibration = $this->readCalibrations();
        
        if(defined('DEBUG')){
            $this->template->debug = true;
        }        
        
//        if(Controller::isAjaxRequest()){
//            return $this->template->dispatchPartial('test/testws');
//        }         
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
        $colorrows = parse_ini_file(TESTWS_COLORI_RIFERIMENTO, true);
        foreach($colorrows as $id => $values){
            $min = preg_split('/\s+/', ltrim($values['min']));
            $lab = preg_split('/\s+/', ltrim($values['mid']));
            $max = preg_split('/\s+/', ltrim($values['max']));
            
            $color = array(
                'min' => array('L' => $min[0],'a' =>  $min[1],'b' =>  $min[2]),
                'lab' => array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]),
                'max' => array('L' => $max[0],'a' =>  $max[1],'b' =>  $max[2])
            );
            
            if($id == 'GREEN'){
                $color['final'] = true;
            }            
            
            $colors[$id] = $color;   
        }
                    
        $angles = array(36, 72, 252, 278);
        $session['TestWS']['initColors'] = $colors;
        $session['TestWS']['angle'] = $angles[rand(0, (count($angles)) - 1)];
        
        $keys = array_keys($colors);
        while(!shuffle($keys)){};   
        
        $shuffledColor = array();
        foreach($keys as $key){
            $shuffledColor[$key] = $colors[$key];
        }
            
        $session['TestWS']['colors'] = $shuffledColor;
        
        $this->setSession($session);
        return $session;
    }
    
    
    
    protected function exportToFile($colors){
        
        $session = $this->getSession();
        
        
        $output = "";        
        $output .= $_SESSION['sessionName']."\t";
        $output .= "White\Black\t";
        
        foreach(array_keys($session['TestWS']['initColors']) as $id){
            $color = $colors->{$id};
            $output .= number_format($color->lab->L, 2, ',', '.')."\t".
                       number_format($color->lab->a, 2, ',', '.')."\t".
                       number_format($color->lab->b, 2, ',', '.')."\t";
        }           
        
        foreach(array_keys($session['TestWS']['colors']) as $id){
            $output .= $id{0}." ";
        }
        
        $output .= "\t".$session['TestWS']['angle']."\n";        

        $this->filename = $this->writeResult($output);
        $session['TestWS']['output'] = $output;
        $session['TestWS']['filename'] = $this->filename;
        $this->setSession($session);
        return $this->filename;        
              
    }
    
}
