<?php

Lib_MainException::$debug = true;
Lib_MainException::setupEnvironment(dirname(__FILE__).DIRECTORY_SEPARATOR.'phperror.log');
Lib_MainException::setupHandlers(); 

class Lib_MainException extends Exception {
   
    public static $debug = false; 
    public $file = ""; 
    public $line = "";
    public static $showHeaders = false;
     
    public static function setupEnvironment($error_log){
        if(self::$debug)
        {
            ini_set('error_reporting', E_ALL); // | E_STRICT
            ini_set('display_errors', 'On');
            ini_set('display_startup_errors', 'On');
            ini_set('html_errors', 'On');
        } 
        else 
        {
            ini_set('error_reporting', E_ERROR);
            ini_set('display_errors', 'Off');
            ini_set('display_startup_errors', 'Off');
            ini_set('html_errors', 'Off');
        }

        
        ini_set('docref_root', '');
        ini_set('docref_ext', '');

        ini_set('log_errors', 'On');
        ini_set('log_errors_max_len', 0);
        ini_set('ignore_repeated_errors', 'Off');
        ini_set('ignore_repeated_source', 'Off');
        ini_set('report_memleaks', 'Off');
        ini_set('track_errors', 'On');
        ini_set('xmlrpc_errors', 'Off');
        ini_set('xmlrpc_error_number', 'Off');
        ini_set('error_prepend_string', '');
        ini_set('error_append_string', '');
        ini_set('error_log', $error_log);
    }
    //-----------------------------------------------------------------------------------------------------------------------
    public static function setupHandlers($errorTypesHandle = null)
    {
        if(is_null($errorTypesHandle)){
            $errorTypesHandle = E_ALL | E_STRICT;
        }                               
        
        set_error_handler(__CLASS__.'::errorHandler', $errorTypesHandle);
        set_exception_handler(__CLASS__.'::exceptionHandler');        
    }
    //-----------------------------------------------------------------------------------------------------------------------
    public static function exceptionHandler($exception)
    {
        if(self::$debug)
        {
            if(!self::$showHeaders)
            {
                header('Content-Type:text/html; charset=utf-8');
                self::$showHeaders=true;
            }
            echo "<b>exception in ".$exception->getFile().", line: ".$exception->getLine()."</b>: <br />".$exception->getMessage()."<br />"; 
        }
                                            
        error_log ($exception, 3, dirname(__FILE__).DIRECTORY_SEPARATOR.'phpexcep.log'); 
            
    }
    //-----------------------------------------------------------------------------------------------------------------------
    public static function errorHandler($severity, $message, $file, $line ) {
        if(self::$debug)
        {
            if(!self::$showHeaders)
            {
                header('Content-Type:text/html; charset=utf-8');
                self::$showHeaders = true;
            }

            echo "<b>error in ".$file. ", line: ".$line."</b>: <br />".$message."<br />";    
        }
        error_log($message);        
    }
}


?>