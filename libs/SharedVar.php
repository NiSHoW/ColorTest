<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SharedVariable
 *
 * @author Nisho
 */
class SharedVar {
    
    Const APC = 'APC';
//    Const SEMAPHOR = 'SEMAPHOR';
    Const FILE = 'APC';
    Const XCACHE = 'XCACHE';
    
    private static $instance = NULL;
    
    private $method = null;
    
    protected function __construct() {
        $this->checkBetterMethod();
    }
    
    /**
     * return instance of sharedmemory
     * @return type
     */
    public static function getInstance(){
        if(!(self::$instance instanceof SharedVar)){
            self::$instance == new SharedVar();
        }
        print_r(self::$instance);
        die();
        return self::$instance;
    }
    
    
    /**
     * Exists var?
     * @param type $var
     * @return type
     */
    public function exists($var){
        switch ($this->method){
            case SharedVar::APC:
                return apc_exists($var);
            case SharedVar::XCACHE:
                return xcache_isset($var);
//            case SharedMemory::SEMAPHOR :
//                return false;  
            case SharedVar::FILE:
                return readStatus($var) !== false;
        }
    }    
    
    /**
     * Store or update var
     * @return type
     */
    public function get($var){
        switch ($this->method){
            case SharedVar::APC:
                return apc_get($var);
            case SharedVar::XCACHE:
                return xcache_get($var);
//            case SharedMemory::SEMAPHOR :
//                return false;  
            case SharedVar::FILE:
                return readStatus($var);
        }
    }        
    
    /**
     * Store or update var
     * @return type
     */
    public function store($var, $value){
        switch ($this->method){
            case SharedVar::APC:
                return apc_store($var, $value);
            case SharedVar::XCACHE:
                return xcache_set($var, $value);
//            case SharedMemory::SEMAPHOR :
//                return false;  
            case SharedVar::FILE:
                return updateStatus($var, $value);
        }
    }    
    
    public function inc($var){
        switch ($this->method){
            case SharedVar::APC:
                return apc_inc($var);
            case SharedVar::XCACHE:
                return xcache_inc($var);
//            case SharedMemory::SEMAPHOR :
//                return false;  
            case SharedVar::FILE:
                return incrementStatus($var);
        }
    }
    
    public function dec($var){
        switch ($this->method){
            case SharedVar::APC:
                return apc_dec($var);
            case SharedVar::XCACHE:
                return xcache_dec($var);
//            case SharedMemory::SEMAPHOR :
//                return false;  
            case SharedVar::FILE:
                return decrementStatus($var);
        }
    }
    
    
    /**
     * Check best method supported
     */
    private function checkBetterMethod(){
        if(function_exists('apc_cache_info')){
           $this->method = SharedVar::APC;
        } else if(function_exists('xcache_set')) {
           $this->method = SharedVar::XCACHE;
//        } else if(function_exists('sem_get')){
//           $this->method = SharedMemory::SEMAPHOR; 
        } else {
            $this->method = SharedVar::FILE; 
        }
    }
    
    /**
     * Read status for file method
     * @param type $var
     * @return boolean
     */
    private function readStatus($var) {
        $f = fopen('./'.$var.'.lock', 'r');
        if (!$f) return false;
        if (flock($f, LOCK_SH)) {
            $ret = fread($f, 8192);
            flock($f, LOCK_UN);
            fclose($f);
            return $ret;
        }
        fclose($f);
        return false;
    }

    /**
     * Update status for file method
     * @param type $var
     * @return boolean
     */
    private function updateStatus($var, $new) {
        $f = fopen('./'.$var.'.lock', 'w');
        if (!$f) return false;
        if (flock($f, LOCK_EX)) {
            ftruncate($f, 0);
            fwrite($f, $new);
            flock($f, LOCK_UN);
            fclose($f);
            return true;
        }
        fclose($f);
        return false;
    }

    /**
     * Inc status for file method
     * @param type $var
     * @return boolean
     */
    private function incrementStatus($var) {
        $f = fopen('./'.$var.'.lock', 'rw');
        if (!$f) return false;
        if (flock($f, LOCK_EX)) {
            $current = fread($f, 8192);
            $current++;
            ftruncate($f, 0);
            fwrite($f, $current);
            flock($f, LOCK_UN);
            fclose($f);
            return true;
        }
        fclose($f);
        return false;
    }
    
    /**
     * Dec status for file method
     * @param type $var
     * @return boolean
     */
    private function decrementStatus($var) {
        $f = fopen('./'.$var.'.lock', 'rw');
        if (!$f) return false;
        if (flock($f, LOCK_EX)) {
            $current = fread($f, 8192);
            $current--;
            ftruncate($f, 0);
            fwrite($f, $current);
            flock($f, LOCK_UN);
            fclose($f);
            return true;
        }
        fclose($f);
        return false;
    }    
    
}
