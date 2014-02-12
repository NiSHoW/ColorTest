<?php

/**
 * Description of TestClass
 *
 * @author Nisho
 */

include_once APPLICATION_LIBS.'/Controller.php';
        

abstract class TestController extends Controller{

    protected $filename = '';
    
    protected function checkSession(){}    
    
    public function exportFileAction(){
        $this->checkSession();

        // fix for IE catching or PHP bug issue
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        // browser must download file from server instead of cache

        // force download dialog
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // use the Content-Disposition header to supply a recommended filename and
        // force the browser to display the save dialog.
        header("Content-Disposition: attachment; filename=".basename($this->filename).";");

        /*
        The Content-transfer-encoding header should be binary, since the file will be read
        directly from the disk and the raw bytes passed to the downloading computer.
        The Content-length header is useful to set for downloads. The browser will be able to
        show a progress meter as a file downloads. The content-lenght can be determines by
        filesize function returns the size of a file.
        */
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($this->filename));

        @readfile($this->filename);
        exit(0);

    }    
    
    
    protected function readCalibrations(){
        $calibration = parse_ini_file(CALIBRAZIONE);
        return array(
            $calibration['xR'],
            $calibration['yR'],
            $calibration['zR'],
            $calibration['xG'],
            $calibration['yG'],
            $calibration['zG'],
            $calibration['xB'],
            $calibration['yB'],
            $calibration['zB'],
            $calibration['xW'],
            $calibration['yW'],
            $calibration['zW'],
            $calibration['gamma_R'],
            $calibration['gamma_G'],
            $calibration['gamma_B'],
        );
    }        
    
    
    protected function writeResult($output){
        
        $filename = date("y-m-d", time()).".txt";
        $fp = fopen(APPLICATION_RESULTS."/".$filename, "a+");

        if (flock($fp, LOCK_EX)) { // do an exclusive lock
            fwrite($fp, $output);
            flock($fp, LOCK_UN); // release the lock
        } else {
            echo "Couldn't lock the file !";
        }

        fclose($fp);
        
        return $filename;
    }
    
}
