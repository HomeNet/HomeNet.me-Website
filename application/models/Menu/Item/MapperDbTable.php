<?php

/*
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
 * @package Core
 * @subpackage Menu_Item
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Menu_Item_MapperDbTable implements Core_Model_Menu_Item_MapperInterface {

    protected $_table = null;

    /**
     * @return Core_Model_DbTable_Menu_Item;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('menu_items');
            $this->_table->setRowClass('Core_Model_Menu_Item_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }

//   public function fetchObjectByUrl($url){
//       $select = $this->getTable()->select()->where('url = ?',$url);
//       return $this->getTable()->fetchRow($select);
//    }
    
    public function fetchObjectsByMenu($menu){
       $select = $this->getTable()->select()->where('`menu` = ?',$menu);
       return $this->getTable()->fetchAll($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Core_Model_Menu_Item_Interface $object) {

        if (($object instanceof Core_Model_Menu_Item_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
            if(empty($row)){
               $row = $this->getTable()->createRow();
            }

        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
       // die(debugArray($row));
        try {
        $row->save();
        } catch(Exception $e){
            if(strstr($e->getMessage(), '1062 Duplicate')) {
               throw new DuplicateEntryException("URL Already Exists"); 
           //' } elseif(strstr($e->getMessage(), '1048 Column')) {
           //    throw new InvalidArgumentException("Invalid Column"); 
            } else {
                 throw new Exception($e->getMessage());
            }
        }

        return $row;
    }

    public function delete(Core_Model_Menu_Item_Interface $object) {

        if (($object instanceof Core_Model_Menu_Item_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Menu_Item');
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        } //if (APPLICATION_ENV != 'production') {
    }
}