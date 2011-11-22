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
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Zend_Db_Table('homenet_houses');
            $this->_table->setRowClass('HomeNet_Model_House_DbTableRow');
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


    public function fetchObjectById($id){
        $result = $this->getTable()->find($id)->current();
        return $result;

    }

     public function fetchObjectsByIds($ids){
        $select = $this->getTable()->select()->where('id in (?)', $ids);
        $result = $this->getTable()->fetchAll($select);
        
        $houses = array();

         foreach($result as $key => $house){
            $houses[$house->id] = $house;
        }

        return $houses;
    }

    public function fetchObjectByIdWithRooms($id){
        $house = $this->getTable()->find($id)->current();
        
        $rooms = $this->getRoomsMapper()->fetchRoomsByHouse($id);

        foreach($rooms as $key => $room){

             $house->rooms[$room->id] = $room;
        }

        return $house;
    }

     public function fetchObjectsByIdsWithRooms($ids){

        $houses = $this->fetchHousesByIds($ids);
     
        $rooms = $this->getRoomsMapper()->fetchRoomsByHouses($ids);

        foreach($rooms as $key => $room){

             $houses[$room->house]->rooms[$room->id] = $room;
        }
        
        return $houses;
    }

    public function save(HomeNet_Model_House_Interface $object){
        
        if(($object instanceof HomeNet_Model_DbTableRow_Room) && ($object->isConnected())){
            return $object->save();
        }
        elseif(!is_null($object->id)){
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
        return $row->save();
    }

    public function delete(HomeNet_Model_House_Interface $object){
        if (($object instanceof HomeNet_Model_DbTableRow_House) && ($object->isConnected())) {
            return $object->delete();
        } elseif (!is_null($object->id)) {
            return $this->getTable()->find($object->id)->current()->delete();
        }

        throw new InvalidArgumentException('Invalid House');
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}