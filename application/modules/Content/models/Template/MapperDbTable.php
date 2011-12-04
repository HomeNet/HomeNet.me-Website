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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

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
        if ($this->_table === null) {
             $this->_table = new Zend_Db_Table('content_templates');
            $this->_table->setRowClass('Content_Model_Template_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
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
   public function fetchObjectsBySection($section){

       $select = $this->getTable()->select()->where('section = ?',$section)->where('active = 1')->group('id');//->where(new Zend_Db_Expr('MAX(revision) = revision')); //;
       return $this->getTable()->fetchAll($select); //from($this->getTable(),array(new Zend_Db_Expr('*'),new Zend_Db_Expr('MAX(revision)')))->
    }  
    
    /**
     *
     * @param string $url
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectBySectionUrl($section, $url){

       $select = $this->getTable()->select()->where('section = ?',$section)->where('url = ?',$url)->where('active = 1')->limit(1);
       return $this->getTable()->fetchRow($select);
    }
    /**
     *
     * @param string $id
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectById($id){

       $select = $this->getTable()->select()->where('id = ?',$id)->where('active = 1')->limit(1);
       return $this->getTable()->fetchRow($select);
    }
    
    /**
     *
     * @param string $id
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchRevisionsById($id){

       $select = $this->getTable()->select()->from($this->getTable(),array('id','revision', 'owner', 'active', 'section', 'url'))->where('id = ?',$id)->where('active = 1')->limit(1);
       return $this->getTable()->fetchRow($select);
    }


//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }



    public function save(Content_Model_Template_Interface $object) {

        if (($object instanceof Content_Model_Template_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif (!is_null($object->id) && !is_null($object->revision)) {
            $row = $this->getTable()->find($object->id,$object->revision)->current();
            if(empty($row)){
               $row = $this->getTable()->createRow();
            }

        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
       // die(debugArray($row));
        $object = $row->save();
        
        if($object->active == true){
            $where1 = $this->getTable()->getAdapter()->quoteInto('id = ?', $object->id);
            $where2 = $this->getTable()->getAdapter()->quoteInto('revision != ?', $object->revision);
 
            $this->getTable()->update(array('active' => false), array($where1, $where2));

        }

        return $row;
    }

    public function delete(Content_Model_Template_Interface $object) {

        if (($object instanceof Content_Model_Template_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif (!is_null($object->id) && !is_null($object->revision)) {
            $row = $this->getTable()->find($object->id,$object->revision)->current();
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