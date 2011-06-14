<?php

class HomeNet_Model_Navigation extends Zend_Navigation {

    private $_house = null;
    private $_region = null;

    public function __construct($houses) {

        

        $container = $this->_buildHouses($houses);
        $this->addPages($container);



    }

    private function _getRegions($house){

        $service = new HomeNet_Model_House_Service();
        $regions = $service->getHouseRegionNames($house->id);


        //$regions = $house['regions'];

        foreach($house->rooms as $room){
            $regions[$room['region']]['rooms'][] = $room;
        }

        //die(debugArray($regions));

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
        
        foreach($regions as $region){
            if(!empty($region->rooms)){
                $this->_region = $region['id'];
                //http://homenet.me/home/2345/g
                $container[] = array(
                    'class' => 'house-region',
                'label' => $region['name'],
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
            'route' => 'homenet-room',
            'params' => array('house' => $this->_house, 'region' => $this->_region, 'room' => $room->id));
        }
//        $container[] = array(
//            'label' => 'New',
//            'class' => 'room-new',
//            'route' => 'homenet-room-add',
//            'params' => array('house' => $this->_house, 'region' => $this->_region));
        return $container;
    }

}

