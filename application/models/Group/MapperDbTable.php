<?php

/*
 * ApikeyMapperDbTable.php
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

require "MapperInterface.php";

/**
 * @package Core
 * @subpackage Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Group_MapperDbTable implements Core_Model_Group_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Core_Model_DbTable_Group;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Core_Model_DbTable_Groups();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }







    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }

//   public function fetchObjectsBySection($section){
//
////       if(is_null($user)){
////           $u = new Zend_Session_Namespace('User');
////           $user = $u->id;
////        }
////
////       $select = $this->getTable()->select()->where('user = ?',$user)
////                                ->where('house = ?',$house);
////
////       return $this->getTable()->fetchAll($select);
//    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Core_Model_Group_Interface $group) {

        if (($group instanceof Core_Model_DbTableRow_Group) && ($group->isConnected())) {
            return $group->save();
        } elseif (!is_null($group->id)) {
            $row = $this->getTable()->find($group->id)->current();
            if(empty($row)){
               $row = $this->getTable()->createRow();
            }

        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($group->toArray());
       // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(Core_Model_Group_Interface $group) {

        if (($group instanceof Core_Model_DbTableRow_Group) && ($group->isConnected())) {
            $group->delete();
            return true;
        } elseif (!is_null($group->id)) {
            $row = $this->getTable()->find($group->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Group Object');
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV == 'testing'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}