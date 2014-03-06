<?php
    
    /**
    *   предположим что нам надо добавить 3-ех этажная здание
    *   на первом этаже есть 2 комнаты, кухня
    *   на втором 4, и у каждого есть, свой балкон
    *   на третем 2, и у одного есть балкон
    * 
    *   сперва создадим дом (type->{building, garage, market, ... } ) по конкретному адресу, типу, цену, по плошади 
    *   и добавляем параметр (resident_type) который будет означать что это жилой дом
    *   resident_type->{1:residential, 0:non-residential }
    *   id: уникальный номер realEstate
    * 
    */
    
    class Controllers_Main
    {
        /*
        *   массив для хранения объектов RealEstate 
        */ 
        public $realEstateArray;
        public $renderObjectArray;
        public $db;
        
        
        
        function __construct()
        {
            // можнo так же использовать PDO
            // $this->db=new Db_MySQLi();
            
            $this->realEstateArray    = array();    
            $this->renderObjectArray  = array();
            $this->renderObjectArray["realestat"] = array();
            
        }
        //-----------------------------------------------------------------------------------------------------------------
        function fRun()
        {
            $this->fInit();
            $this->fFiltering();
            $this->fSorting();
        }
        //-----------------------------------------------------------------------------------------------------------------
        function fInit()
        {
            array_push($this->realEstateArray, new Model_RealEstateBldg());
            end($this->realEstateArray)->initRealEstate(array(
                                                "id" =>300,
                                                "type"=>"building",
                                                "address"=>"address 1", 
                                                "cost"=>100000 /* USD */,
                                                "square"=>500 /* квадратный метр  */,
                                                "resident_type"=>1
                                                ));
                                                                                                                
            
            /*
            *  добавляем атрибуты, 
            *  у разных недвижимости будут атрибуты разных количеств
            */ 
            end($this->realEstateArray)->setAttribute(array(
                                                "floor_cnt"=>3,
                                                "room_cnt"=>8,
                                                "balcony_cnt"=>5                                         
                                                ));
                                                
            /*
            *   добавляем части
            *   добавляем этажи месте с ним и атрибуты 
            * 
            *   этаж 1
            */  
            $part = end($this->realEstateArray)->setPart(array(
                                                "part_type"=>"floor",
                                                "attribute"=>array(
                                                                    "floor_nomer"=>1, 
                                                                    "room_cnt"=>2,
                                                                    "kitchen"=>1
                                                 )));
            
            /*
            *   в этаже 1 добавляем part:кухня с ним и атрибуты
            */ 
            $part->setPart(array(
                                "part_type"=>"kitchen",
                                "attribute"=>array(
                                                    "square"=>1, 
                                                    "kitchen_furniture"=>1 /*0:нет, 1:есть */                                            
                                 )));

                                                  
            /*
            *   добавляем этаж 2
            */ 
            $part = end($this->realEstateArray)->setPart(array(
                                                "part_type"=>"floor",
                                                "attribute"=>array(
                                                                    "floor_nomer"=>2, 
                                                                    "room_cnt"=>4,
                                                                    "balkon_cnt"=>4
                                                )));
            
              
            /*
            *   этаж 3    
            */ 
            $part =  end($this->realEstateArray)->setPart(array(
                                                "part_type"=>"floor",
                                                "attribute"=>array(
                                                                    "floor_nomer"=>3, 
                                                                    "room_cnt"=>2,
                                                                    "balkon_cnt"=>1
                                                )));
             
            /**
            *   добавляем второе здание
            */ 
            array_push($this->realEstateArray, new Model_RealEstateBldg());
            end($this->realEstateArray)->initRealEstate(array(
                                                "id" =>9,
                                                "type"=>"building",
                                                "address"=>"address 1", 
                                                "cost"=>80000 /* USD */,
                                                "square"=>200, /* квадратный метр  */
                                                "resident_type"=>1
                                                ));
            
            /**
            *   добавляем третье здание
            */ 
            array_push($this->realEstateArray, new Model_RealEstateBldg());
            end($this->realEstateArray)->initRealEstate(array(
                                                "id" =>2,
                                                "type"=>"building",
                                                "address"=>"address 1", 
                                                "cost"=>100000 /* USD */,
                                                "square"=>250, /* квадратный метр  */
                                                "resident_type"=>0
                                                ));
                    
        }
        //-----------------------------------------------------------------------------------------------------------------
        function fFiltering()
        {
            //____________________________________________________________________________________________________________________FILTERING AND SORTING
            /*
            *   конечно филтри и сортировки в база намного проще делать
            *   используя  "where" / "order by" ну предположем что на данной ситуацыии это невозможно и нодо делать с помошю php   
            * 
            *   задание: надо получить список нежилых недвижимостей у которых 
            *       90000  <= сумма    <=  110000 
            *       200    <= площадь  <=  400
            */             
            Lib_UTILRealEstate::$store = array(
                                        "resident_type"=>0,
                                        "cost"=>array(90000, 110000),
                                        "square"=>array(200, 400)
                                     );                       
            $this->renderObjectArray["realestat"]["filter"] = array_filter($this->realEstateArray, 'Lib_UTILRealEstate::execFilter');
        }
        //-----------------------------------------------------------------------------------------------------------------
        function fSorting()
        {
            /*
            *   сортировать с параметрами по цене и по площади
            */
            $this->renderObjectArray["realestat"]["sorting"] = Lib_UTILRealEstate::execSorting(array("square", "cost"), $this->realEstateArray);    
        }
        //-----------------------------------------------------------------------------------------------------------------
        function fRender()
        {
            require_once "template/header.php";
            require_once "template/content.php";
            require_once "template/footer.php";
            
        }
        //-----------------------------------------------------------------------------------------------------------------
        
        
    }

?>