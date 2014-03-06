<?php 
/*
*   RealEstate будет базовым классом, для объявления недвижимости, 
*   
* 
*   id                  - уникал ID в системе    
*   type                - тип недвижимости { дом, квартира, магазин, отель, завод  и т.д }
*   address             - адрес недвижимости
*   cost                - цена недвижимости USD
*   square              - площадь в кв. метрах
*   resident_type       - если 1: жилиой  0: не жилой  
* 
*    
*/

class Model_RealEstate
{       
    public      $objRealEstate;
    protected   $objAttribute;
    
    
    
    function __construct()
    {   
        $this->objAttribute = array();   
    }
    //-------------------------------------------------------------------------------------
    function initParam()
    {    
        $this->objRealEstate = array();
                
        $this->objRealEstate["id"]="";                
        $this->objRealEstate["type"]="";              
        $this->objRealEstate["address"]="";           
        $this->objRealEstate["cost"]=0;               
        $this->objRealEstate["square"]=0;
        $this->objRealEstate["resident_type"]=0;
    }
    //-------------------------------------------------------------------------------------
    final public function initRealEstate(array $data) 
    {
        
        $this->initParam();
        foreach($this->objRealEstate as $key => $val)
        {
            if(isset($data[$key]))
            {
                $this->objRealEstate[$key]=$data[$key];
            }   
            else
            {
                throw new MainException("информация не достаточно ");     
            }
        }
    }
    //-------------------------------------------------------------------------------------    
    function __set($name, $val) 
    {   
        // потом используем
    } 
    //-------------------------------------------------------------------------------------
    function __get($name) 
    {   
        // потом используем
    } 
    //-------------------------------------------------------------------------------------
    function __destruct() 
    {   
    }
    //-------------------------------------------------------------------------------------
}                        
         

?>