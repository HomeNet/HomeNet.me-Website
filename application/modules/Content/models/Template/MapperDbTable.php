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

require_once "MapperInterface.php";

/**
 * @package Content
 * @subpackage Template
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Template_MapperDbTable implements Content_Model_Template_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Content_Model_DbTable_Template;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Content_Model_DbTable_Template();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }




    /**
     * Fetch Section Content Object by Id
     * 
     * @param int $id 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectByIdRevision($id,$revision){
        return $this->getTable()->find($id,$revision)->current();
    }
    
   /**
     * Fetch Section Content Objects by Section
     * 
     * @param int $section 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
   public function fetchNewestObjectsBySection($section){

       $select = $this->getTable()->select()->from($this->getTable(),array(new Zend_Db_Expr('*'),new Zend_Db_Expr('MAX(revision)')))->where('section = ?',$section)->group('id');
       return $this->getTable()->fetchAll($select);
    }  
    
    /**
     *
     * @param string $url
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchNewestObjectBySectionUrl($section, $url){

       $select = $this->getTable()->select()->where('section = ?',$section)->where('url = ?',$url)->order('revision DESC')->limit(1);
       return $this->getTable()->fetchRow($select);
    }
    /**
     *
     * @param string $id
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchNewestObjectById($id){

       $select = $this->getTable()->select()->where('id = ?',$id)->order('revision DESC')->limit(1);
       return $this->getTable()->fetchRow($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Content_Model_Template_Interface $content) {

        if (($content instanceof Content_Model_DbTableRow_Template) && ($content->isConnected())) {
            return $content->save();
        } elseif (!is_null($content->id) && !is_null($content->revision)) {
            $row = $this->getTable()->find($content->id,$content->revision)->current();
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

    public function delete(Content_Model_Template_Interface $content) {

        if (($content instanceof Content_Model_DbTableRow_Template) && ($content->isConnected())) {
            $content->delete();
            return true;
        } elseif (!is_null($content->id) && !is_null($content->revision)) {
            $row = $this->getTable()->find($content->id,$content->revision)->current();
            $row->delete();
            return true;
        }

        throw new Exception('Invalid Content');
    }
    
    public function deleteBySection($section){
 
         $where = $this->getTable()->getAdapter()->quoteInto('section = ?',$section);
         $this->getTable()->delete($where);
    }
    
    public function deleteById($id){
 
         $where = $this->getTable()->getAdapter()->quoteInto('id = ?',$id);
         $this->getTable()->delete($where);
    }
    
    public function deleteAll(){
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}