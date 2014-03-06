<?php 
    /*
*   RealEstateBldg будет хранить всю информацию об объекте, 
*   и оно наследуют от класса RealEstate. 
* 
*   В далнейшем можно и создать RealEstateArea для хранение информацыю 
*   об земельных участков, фруктовых садах, виноградниках и т.д.
*   
* 
*   В RealEstateBldg будет две основные методы 
*   
*   1) setAttribute    -   для добавление атрибутов 
*                           количество комнат, количество этажей, наличия бассейна, общее состояние, ???
*                           для отеля количество звезд, для завода тип сфера деятельности и т.д., 
* 
*   2) setPart         -    для добавление дополнительных информации об отдельных частях недвижимости, к примеру
*                            1) наличия кухни,  их плошадь отдельно, обстановка в каждом
*                            2) наличия балконов
*                            3) и т.д.
* 
*   part будет типом RealEstateBldg ,  part = new RealEstateBldg();
* 
*   здесь можно реализовать parent <-> child концепцию 
*   
*    
*/

class Model_RealEstateBldg extends Model_RealEstate
{
    const   RESIDENTIALPROPERTY=1;
    const   NONRESIDENTIALPROPERTY=0;
    
    public      $residental_type;
    public   $objPart;
    
    function __construct()
    {
        parent::__construct();        
        $this->objPart = array();
    }
    //-------------------------------------------------------------------------------------
    public function setAttribute($data)
    {
        foreach($data as $key=>$val)
        {
            $this->objAttribute[$key] = $val;
        }
    }   
    //-------------------------------------------------------------------------------------
    public function setPart($data)
    {
        
        array_push($this->objPart, array(
                                            "part_type"=>$data["part_type"],
                                            "part"=>new Model_RealEstateBldg(),
                                        ));
        
        $p = end($this->objPart);
        $p["part"]->setAttribute($data["attribute"]);
        
        return $p["part"];
    } 
    //-----------------------------------------------------------------------------
    function __set($name, $val) 
    {    
        // потом используем 
    } 
    //-----------------------------------------------------------------------------
    function __get($name) 
    { 
        // потом используем
    } 
    //-----------------------------------------------------------------------------
    function __destruct() 
    {  
        parent::__destruct();         
    }    
    //-----------------------------------------------------------------------------
}           

?>