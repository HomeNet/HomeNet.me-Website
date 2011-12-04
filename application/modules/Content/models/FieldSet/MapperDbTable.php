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
 * @package Content
 * @subpackage FieldSet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Content_Model_FieldSet_MapperDbTable implements Content_Model_FieldSet_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table_Abstract;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('content_field_sets');
            $this->_table->setRowClass('Content_Model_FieldSet_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }



    public function fetchObjects(){
        return $this->getTable()->fetchAll();
    }



    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }

   public function fetchObjectsBySection($section){

//       if(is_null($user)){
//           $u = new Zend_Session_Namespace('User');
//           $user = $u->id;
//        }
//
       $select = $this->getTable()->select()->where('section = ?',$section)->order('order ASC');

       return $this->getTable()->fetchAll($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }

    public function shiftOrderBySection($section, $currentPosition, $newPosition = null, $id = null) {
        //@todo determine if id is really needed 
        if ($newPosition == $currentPosition) { //same position, do nothing 
            return true;
        }

        $where = array();
        $where[] = $this->getTable()->getAdapter()->quoteInto('`section` = ?', $section);

        if(!is_null($id)){
            $where[] = $this->getTable()->getAdapter()->quoteInto('`id` != ?', $id);
        }
        
        if(is_null($newPosition) && !is_null($currentPosition)){ //delete
            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` > ?', $currentPosition);
            $data = array('order' => new Zend_Db_Expr('`order` - 1'));
            
        } elseif(!is_null($newPosition) && is_null($currentPosition)){ //create
            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` >= ?', $newPosition);
            $data = array('order' => new Zend_Db_Expr('`order` + 1'));
            
        } elseif($newPosition > $currentPosition) { //moving up
            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` > ?', $currentPosition);
            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` <= ?', $newPosition);
            $data = array('order' => new Zend_Db_Expr('`order` - 1'));
            
        } elseif ($newPosition < $currentPosition) { //moving down

            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` < ?', $currentPosition);
            $where[] = $this->getTable()->getAdapter()->quoteInto('`order` >= ?', $newPosition);
            $data = array('order' => new Zend_Db_Expr('`order` + 1'));
        } else { //same position, do nothing 
            return true;
        }
      //  die(debugArray($where));
        return $this->getTable()->update($data, $where);
    }

    public function setObjectOrder($object, $newPosition) {

        if (!$this->shiftOrderBySection($object->section, $object->order, $newPosition, $object->id)) {
            throw new Exception('Error Shifting Fields');
        }

        //update order
        $object->order = $newPosition;
        return $this->save($object);
    }
    


    public function save(Content_Model_FieldSet_Interface $object) {

        if (($object instanceof Content_Model_FieldSet_DbTableRow) && ($object->isConnected())) {
            return $object->save();;
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
        $row->save();

        return $row;
    }

    public function delete(Content_Model_FieldSet_Interface $object) {

        if (($object instanceof Content_Model_FieldSet_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return true;
        }

        throw new Exception('Invalid Content');
    }
    
   public function deleteBySection($section){
         $where = $this->getTable()->getAdapter()->quoteInto('section = ?',$section);
         $this->getTable()->delete($where);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}