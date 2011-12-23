<?php

class HomeNet_Model_Navigation extends Zend_Navigation {

    private $_house = null;
    private $_region = null;

    public function __construct($houses) {

       // var_dump($houses);

        $container = $this->_buildHouses($houses);
        $this->addPages($container);
    }

    private function _getRegions($house){

        $service = new HomeNet_Model_House_Service();
        $regions = $service->getRegionsById($house->id);
        //$regions = $service->getRegions();
 
        
        $regions = array_fill_keys($regions, array());

        $rooms = $house->getRooms();
 
        foreach($rooms as $room){
            $regions[$room['region']]['rooms'][] = $room;
        }
        
        //die(debugArray($regions));

        //exit;

        return $regions;
    }
 
    private function _buildHouses($houses){
        $container = array();
        //$container['class'] = 'menu';

        foreach($houses as $house){
            if(!empty($house)){
            //http://homenet.me/home/2345
            $this->_house = $house->id;

            $container[] = array(
             'class' => 'ui-widget-header ui-corner-top',
            'label' => $house->name,
            'title' => $house->description,
            'route' => 'homenet-house',
            'params' => array('house' => $house->id),
            'pages' => $this->_buildRegions($house));
            }
        }
        //

        return $container;
    }

    private function _buildRegions($house){
        $container = array();

        $regions = $this->_getRegions($house);
        //die(debugArray($regions));
        
        $service = new HomeNet_Model_House_Service();
        $regionNames = $service->getRegions();
        
        foreach($regions as $key=>$region){
            
           // var_dump($region);
            if(!empty($region['rooms'])){
                $this->_region = $key;
                //http://homenet.me/home/2345/g
                $container[] = array(
                    'class' => 'house-region', //-region
                'label' => $regionNames[$key],
                'uri' => '#',
                //'params' => array('house'=>$this->_house, 'region' => $this->_region),
                'pages' => $this->_buildRooms($region['rooms']));
            }
        }
        return $container;
    }
    private function _buildRooms($rooms){
        
        $container = array();
         foreach($rooms as $room){
            //http://homenet.me/home/2345/g/room/3242

            $container[] = array(
            'label' => $room->name,
                
            'title' => $room->description,
            'route' => 'homenet-house-id',
            'params' => array('controller'=>'room','house' => $this->_house, 'region' => $this->_region, 'id' => $room->id));
        }
//        $container[] = array(
//            'label' => 'New',
//            'class' => 'room-new',
//            'route' => 'homenet-room-add',
//            'params' => array('house' => $this->_house, 'region' => $this->_region));
        return $container;
    }

}

