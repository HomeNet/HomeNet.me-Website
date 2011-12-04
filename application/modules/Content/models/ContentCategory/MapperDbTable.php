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
 * @subpackage ContentCategory
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_ContentCategory_MapperDbTable implements Content_Model_ContentCategory_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Content_Model_DbTable_ContentCategory;
     */
    public function getTable() {
        if ($this->_table === null) {           
            $this->_table = new Zend_Db_Table('content_content_categories');
            $this->_table->setRowClass('Content_Model_ContentCategory_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }

   public function fetchObjectByUrl($url){
       $select = $this->getTable()->select()->where('url = ?',$url);
       return $this->getTable()->fetchRow($select);
    }
    
    public function fetchObjectsBySet($set){
       $select = $this->getTable()->select()->where('`set` = ?',$set);
       return $this->getTable()->fetchAll($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Content_Model_ContentCategory_Interface $object) {

        if (($object instanceof Content_Model_ContentCategory_DbTableRow) && ($object->isConnected())) {
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
          //  } elseif(strstr($e->getMessage(), '1048 Column')) {
          //     throw new InvalidArgumentException("Invalid Column"); 
            } else {
                 throw new Exception($e->getMessage());
            }
        }

        return $row;
    }

    public function delete(Content_Model_ContentCategory_Interface $object) {

        if (($object instanceof Content_Model_ContentCategory_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Category');
    }
    
    public function deleteBySection($section){
         $where = $this->getTable()->getAdapter()->quoteInto('section = ?',$section);
         $this->getTable()->delete($where);
    }
    
    public function deleteByContent($section){
         $where = $this->getTable()->getAdapter()->quoteInto('content = ?',$section);
         $this->getTable()->delete($where);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        } //if (APPLICATION_ENV != 'production') {
    }
}