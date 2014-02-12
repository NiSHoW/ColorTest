<?php

/**
 * Description of TestL
 *
 * @author Nisho
 */
include_once APPLICATION_LIBS.'/TestController.php';
        

class TestLController extends TestController{

    
    public function indexAction(){
        if(!$this->checkSession()){
            //invio errore
            echo "ERRORE";
            exit();
        }
        
        $session = $this->getSession();
        $this->template->colors = $session['TestL']['colors'];
        $this->template->angle = $session['TestL']['angle'];
        $this->template->infoEnabled = true;
        $this->template->calibration = $this->readCalibrations();
        
        if(defined('DEBUG')){
            $this->template->debug = true;
        }
        
//        if(Controller::isAjaxRequest()){
//            return $this->template->dispatchPartial('test/testl');
//        }         
        return $this->template->dispatch('test/testl');
        
    }    
    
    public function saveAction(){
        $this->checkSession();
        if(!Controller::isAjaxRequest()){
            return $this->redirect('/');
        }
        
        $session = $this->getSession();
        $session['TestL']['status'] = 'complete';
        $session['TestL']['endTime'] = time();
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
        if(!isset($session['TestL'])){
            $session = $this->initialieSession();
        }
        
        if(isset($session['TestL']['filename'])){
            $this->filename = $session['TestL']['filename'];
        }
        
        if($session['TestL']['status'] == 'complete'){
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
        $session['TestL'] = array(
            'status' => 'init',
            'startTime' => time(),
        ); 
                
        $colors = array();
        $colorrows = parse_ini_file(TESTL_COLORI_RIFERIMENTO, true);
        foreach($colorrows as $id => $values){
            //$min = preg_split('/\s+/', ltrim($values['min']));
            $lab = preg_split('/\s+/', ltrim($values['mid']));
            //$max = preg_split('/\s+/', ltrim($values['max']));
            
            $color = array(
                //'min' => array('L' => $min[0],'a' =>  $min[1],'b' =>  $min[2]),
                'lab' => array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]),
                //'max' => array('L' => $max[0],'a' =>  $max[1],'b' =>  $max[2])
            );
            
            if($id == 'GREEN'){
                $color['final'] = true;
            }            
            
            $colors[$id] = $color;   
        }
        
        $angles = array(36, 72, 252, 278);
        $session['TestL']['initColors'] = $colors;
        $session['TestL']['angle'] = $angles[rand(0, (count($angles)) - 1)];        
        
        $keys = array_keys($colors);
        while(!shuffle($keys)){};   
        
        $shuffledColor = array();
        foreach($keys as $key){
            $shuffledColor[$key] = $colors[$key];
        }
        
        $session['TestL']['colors'] = $shuffledColor;

        $this->setSession($session);
        return $session;
    }    
    
    protected function exportToFile($colors){
        
        $session = $this->getSession();
        
        $output = "";        
        $output .= $_SESSION['sessionName']."\t";
        $output .= "Luminosità\t";
        
        foreach(array_keys($session['TestL']['initColors']) as $id){
            $color = $colors->{$id};
            $output .= number_format($color->lab->L, 2, ',', '.')."\t".
                       number_format($color->lab->a, 2, ',', '.')."\t".
                       number_format($color->lab->b, 2, ',', '.')."\t";
        }           
        
        foreach(array_keys($session['TestL']['colors']) as $id){
            $output .= $id{0}." ";
        }
        
        $output .= "\t".$session['TestL']['angle']."\n";                        

        $this->filename = $this->writeResult($output);
        $session['TestL']['output'] = $output;
        $session['TestL']['filename'] = $this->filename;
        $this->setSession($session);
        return $this->filename;

    }
    
}
