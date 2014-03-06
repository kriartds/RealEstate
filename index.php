<?php 

    /*
    *   1) pdo
    *   2) MVC
    *   3) 
    */

    include "Lib/MainException.php";
    
    /**
    *  Автоматическая загрузка классов
    * 
    *  можно потом создать класс с собственным методам автоматической загрузки классов 
    *  используя SPL стандартную библиотеку 
    */
    function __autoload($class_name) 
    {
        try
        {
            $path = str_replace("_", "/", $class_name);
            //include "class.".$class_name.".php";    
            include $path.".php";
            
        }            
        catch (MainException $e)  
        {
            throw new MainException("cant load file ");     
        }
    }

    
    $page  = new Controllers_Main();
    $page->fRun();
    $page->fRender();
  
?>