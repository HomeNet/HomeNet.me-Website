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

/**
 * @package Content
 * @subpackage FieldSet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

require "MapperInterface.php";

class Content_Model_FieldSet_MapperDbTable implements Content_Model_FieldSet_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Content_Model_DbTable_FieldSet;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Content_Model_DbTable_FieldSets();
        }
        return $this->_table;
    }

    public function setTable($table) {
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
       $select = $this->getTable()->select()->where('section = ?',$section);

       return $this->getTable()->fetchAll($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Content_Model_FieldSet_Interface $content) {

        if (($content instanceof Content_Model_DbTableRow_FieldSet) && ($content->isConnected())) {
            return $content->save();;
        } elseif (!is_null($content->id)) {
            $row = $this->getTable()->find($content->id)->current();
            if(empty($row)){
               $row = $this->getTable()->createRow();
            }

        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($content->toArray());
       // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(Content_Model_FieldSet_Interface $content) {

        if (($content instanceof Content_Model_DbTableRow_FieldSet) && ($content->isConnected())) {
            $content->delete();
            return true;
        } elseif (!is_null($content->id)) {
            $row = $this->getTable()->find($content->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Content');
    }
    
   public function deleteBySection($section){
         $where = $this->getTable()->getAdapter()->quoteInto('section = ?',$section);
         $this->getTable()->delete($where);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
       //     $this->getTable()->delete("id < 10000");
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}