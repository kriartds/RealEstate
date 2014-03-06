<?php 
/*
*   в UTILRealEstate будут реализоваться методы для сортировки и фильтрации
*   
*   количество параметров для фильтрации и сортировки будет неограниченно 
* 
*/
          
class Lib_UTILRealEstate
{
    
    public static $store = array();
    
    //-------------------------------------------------------------------------------------------------------------------------------------------------
    public static function execFilter(Model_RealEstateBldg $obj)
    {
        foreach(self::$store as $key => $val)
        {
            if(is_int(self::$store[$key]))
            {
                if($obj->objRealEstate[$key]!=self::$store[$key])
                {
                    return false;
                }
            }
            
            if(is_array(self::$store[$key]))
            {
                if($obj->objRealEstate[$key]<self::$store[$key][0] && $obj->objRealEstate[$key]>self::$store[$key][1])
                {
                    return false;
                }
            }
            /**
            *  функцию потом можно совершенствовать и поставить другие сравнение
            */                                                   
        }
        
        return true;
    }
    //-------------------------------------------------------------------------------------------------------------------------------------------------
    public static function execSorting(array $column, array $realestate)
    {
        $arrTmpbyId = array();  
        $arrTmp = array();
        
        self::$store = array();
        // мы будем сортировать массив с несколькими ключами
        // по количеству колонок создаем одномерние масивы в $store
        for($i=0; $i<count($column); $i++)
        {
            self::$store[$i] = array();
            
            foreach($realestate as $re_key=>$re_val)
            {         
                self::$store[$i][$re_key] = $re_val->objRealEstate[$column[$i]];
                $arrTmpbyId[$re_val->objRealEstate["id"]]=$re_val; 
            }
        }                                                          
        
        // добавим еще один ключ ID 
        // это технический трюк для получение через ID сам объект поскольку оно уникальный.
        self::$store[$i] = array(); 
        foreach($realestate as $re_key=>$re_val)
        {
            self::$store[$i][$re_key] = $re_val->objRealEstate["id"];
        }
        
        //call_user_func_array('array_multisort', self::$store);
        // можно расширить это функцию и сделать процес сортировку динамическим каличеством ключей,
        // сейчас 2 ключа $store[0], $store[1] еще один ключ для ID $store[2]
        array_multisort(self::$store[0], self::$store[1], self::$store[2]);
                                                                             
        // после сортировки через ID получаем объекты RealEstate
        for($i=0; $i<count($realestate); $i++)
        {
            $arrTmp[]=$arrTmpbyId[self::$store[2][$i]];
        }
        return  $arrTmp;   
    }
    //-------------------------------------------------------------------------------------------------------------------------------------------------
    
    
    
}


?>