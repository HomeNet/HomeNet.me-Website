<?php
/* 
 * HouseMapperDbTable.php
 * 
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * 
 * This file is part of HomeNet.
 * 
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_House_MapperDbTable implements HomeNet_Model_House_MapperInterface {

    protected $_table = null;

    /**
     * @var HomeNet_Model_Room_MapperInterface
     */
    protected $_roomsMapper = null;

    


    
/**
 *
 * @return HomeNet_Model_DbTable_Houses;
 */
    public function getTable(){
        if(is_null($this->_table)){
            $this->_table = new HomeNet_Model_DbTable_Houses();
        }
        return $this->_table;
    }

    public function setTable($table){
        $this->_table = $table;
    }

    /**
     * @return HomeNet_Model_Room_MapperInterface
     */
    public function getRoomsMapper(){
        if(is_null($this->_roomsMapper)){
            $roomsService = new HomeNet_Model_Room_Service();
            $this->_roomsMapper = $roomsService->getMapper();
        }
        return $this->_roomsMapper;
    }

    public function setRoomMapper(HomeNet_Model_RoomsMapperInterface $roomMapper){
        $this->_roomsMapper = $roomMapper;
    }


    public function fetchHouseById($id){
        $row = $this->getTable()->find($id)->current();
        return $row;

    }

     public function fetchHousesByIds($ids){
        $select = $this->getTable()->select()->where('id in (?)', $ids);
        $rows = $this->getTable()->fetchAll($select);
        
        $houses = array();

         foreach($rows as $key => $house){
            $houses[$house->id] = $house;
        }

        return $houses;
    }

    public function fetchHouseByIdWithRooms($id){
        $house = $this->getTable()->find($id)->current();
        
        $rooms = $this->getRoomsMapper()->fetchRoomsByHouse($id);

        foreach($rooms as $key => $room){

             $house->rooms[$room->id] = $room;
        }

        return $house;
    }

     public function fetchHousesByIdsWithRooms($ids){

        $houses = $this->fetchHousesByIds($ids);
     
        $rooms = $this->getRoomsMapper()->fetchRoomsByHouses($ids);

        foreach($rooms as $key => $room){

             $houses[$room->house]->rooms[$room->id] = $room;
        }
        
        return $houses;
    }

    public function save(HomeNet_Model_House_Interface $house){
        
        if(($house instanceof HomeNet_Model_DbTableRow_Room) && ($house->isConnected())){
            $house->save();
            return;
        }
        elseif(!is_null($house->id)){
            $row = $this->getTable()->find($house->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($house->toArray());
        $row->save();
        return $row;
    }

    public function delete(HomeNet_Model_House_Interface $house){
        if (($house instanceof HomeNet_Model_DbTableRow_House) && ($house->isConnected())) {
            $house->delete();
            return true;
        } elseif (!is_null($house->id)) {
            $row = $this->getTable()->find($house->id)->current()->delete();
            return;
        }

        throw new HomeNet_Model_Exception('Invalid House');
    }
}