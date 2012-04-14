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
 * @subpackage Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Content_MapperDbTable implements Content_Model_Content_MapperInterface {

    protected $_table = null;
    protected $_customTables = array();

    /**
     *
     * @return Content_Model_DbTable_Content;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('content_content');
        }
        return $this->_table;
    }

    public function getCustomTable($section) {
        if (!array_key_exists($section, $this->_customTables)) {
            $customTable = new Zend_Db_Table('content_custom_' . $section);
            // die(debugArray($customTable->info(Zend_Db_Table::METADATA)));
            $this->_customTables[$section] = $customTable;
        }
        return $this->_customTables[$section];
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    /**
     * Fetch Section Content Object by Id
     * 
     * @param int $id 
     * @return Content_Model_Content 
     */
    public function fetchObjectByIdRevision($id, $revision) {

        $object = new Content_Model_Content();

        $result = $this->getTable()->find($id)->current();
        if (empty($result)) {
            return null;
        }
        $object->fromArray($result->toArray());

        $result2 = $this->getCustomTable($result->section)->find($id, $revision)->current();

        $object->fromArray($result2->toArray());

        return $object;
    }

    /**
     * Fetch Section Content Objects by Section
     * 
     * @param int $section 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
//    public function fetchObjectsBySection($section) {
//
//        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('section = ?', $section)
//                ->join(array('c' => 'content_custom_' . $section), 'content_content.id = c.id AND 
//                        content_content.active_revision = c.revision'
//        )->order('content_content.active_revision DESC'); //,'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings')
//
//        $results = $this->getTable()->fetchAll($select);
//       // die(debugArray($section));
//        //  $row =  $this->getTable()->fetchRow($select);
//        // $select = $this->getTable()->select()->from($this->getTable(),array(new Zend_Db_Expr('*'),new Zend_Db_Expr('MAX(revision)')))->where('section = ?',$section)->group('id');
//        $objects = array();
//        foreach ($results as $result) {
//            $objects[] = new Content_Model_Content(array('data' => $result->toArray()));
//        }
//
//        return $objects;
//    }
    
    public function fetchObjectsBySection($section, $query = array(), $itemsPerPage = null, $currentPage = 1) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('section = ?', $section)
                ->join(array('c' => 'content_custom_' . $section), 'content_content.id = c.id AND 
                        content_content.active_revision = c.revision'
        ); //,'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings')

        foreach($query as $value){
            //if(!is_array)
            $type = $value[0];
            $column = isset($value[1])?$value[1]:null;
            $value = isset($value[2])?$value[2]:null;
            
            switch($type){
                case 'where':
                    $select->where($column,$value);
                    break;
                case 'orWhere':
                    $select->orWhere($column,$value);
                    break;
                case 'order':
                    $select->order($column.' '.$value);
                    break;
                case 'limit':
                    $select->limit($column, $value);
                    break;
                case 'limitPage':
                    $select->limitPage($column, $value);
                    break;
                default:
                    throw new InvalidArgumentException('Unkown query type: '.$type);
                    break;
            }
            
        }
        
        if($itemsPerPage !== null){
            $select->limitPage($currentPage, $itemsPerPage);
        }
   
        $results = $this->getTable()->fetchAll($select);
       // die(debugArray($section));
        //  $row =  $this->getTable()->fetchRow($select);
        // $select = $this->getTable()->select()->from($this->getTable(),array(new Zend_Db_Expr('*'),new Zend_Db_Expr('MAX(revision)')))->where('section = ?',$section)->group('id');
        $objects = array();
        foreach ($results as $result) {
            $objects[] = new Content_Model_Content(array('data' => $result->toArray()));
        }

        return $objects;
    }
    
     /**
     * Fetch Section Content Objects by Section
     * 
     * @param int $section 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectsBySectionCategory($section, $id, $query = array(), $itemsPerPage = null, $currentPage = 1){

      //  if()
        
        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('content_content.section = ?', $section)
                ->join(array('c' => 'content_custom_' . $section), 'content_content.id = c.id AND 
                        content_content.active_revision = c.revision')
                ->join(array('cat'=>'content_content_categories'), 'content_content.id = cat.content'
        )->where('cat.category = ?',$id);
              //  ->order('content_content.active_revision DESC'); //,'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings')

        
        foreach($query as $value){
            //if(!is_array)
            $type = $value[0];
            $column = isset($value[1])?$value[1]:null;
            $value = isset($value[2])?$value[2]:null;
            
            switch($type){
                case 'where':
                    $select->where($column,$value);
                    break;
                case 'orWhere':
                    $select->orWhere($column,$value);
                    break;
                case 'order':
                    $select->order($column.' '.$value);
                    break;
                case 'limit':
                    $select->limit($column, $value);
                    break;
                case 'limitPage':
                    $select->limitPage($column, $value);
                    break;
                default:
                    throw new InvalidArgumentException('Unkown query type: '.$type);
                    break;
            }
            
        }
        
        if($itemsPerPage !== null){
            $select->limitPage($currentPage, $itemsPerPage);
        }
        
        
        
        
        
        
        
        
        
        
        $results = $this->getTable()->fetchAll($select);
       // die(debugArray($section));
        //  $row =  $this->getTable()->fetchRow($select);
        // $select = $this->getTable()->select()->from($this->getTable(),array(new Zend_Db_Expr('*'),new Zend_Db_Expr('MAX(revision)')))->where('section = ?',$section)->group('id');
        $objects = array();
        foreach ($results as $result) {
            $objects[] = new Content_Model_Content(array('data' => $result->toArray()));
        }

        return $objects;
    }

    /**
     *
     * @param string $url
     * @return Content_Model_Content 
     */
    public function fetchObjectByUrl($url) {

        $object = new Content_Model_Content();

        $select = $this->getTable()->select()->where('url = ?', $url);
        $result = $this->getTable()->fetchRow($select);

        if (empty($result)) {
            return null;
        }

        $object->fromArray($result->toArray());

//        $select = $this->getCustomTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                //->where('section = ?', $section)
//                ->where('url = ?', $url)
//                ->join(array('c' => 'content_custom_' . $section,
//                    'content_content.id = c.id AND 
//                        content_content.active_revision = c.revision')
//        ); //,'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings')

        $result2 = $this->getCustomTable($result->section)->find($result->id, $result->active_revision)->current();
        // = $this->getCustomTable()->fetchRow($select2); 
        $object->fromArray($result2->toArray());

        return $object;
    }

    /**
     *
     * @param string $id
     * @return Content_Model_Content 
     */
    public function fetchObjectById($id) {

        $object = new Content_Model_Content();

        $result = $this->getTable()->find($id)->current();
        if (empty($result)) {
            return null;
        }
        $object->fromArray($result->toArray());
        // die(debugArray($object));
        $result2 = $this->getCustomTable($result->section)->find($result->id, $result->active_revision)->current();
        
        //if the table is out of sync, get last known revision
        if(empty($result2)){
            /*@todo log that the table is out of sync
             */
            $select = $this->getCustomTable($result->section)->select()->where('id = ?',$result->id)->order('revision DESC')->limit(1);
            $result2 = $this->getCustomTable($result->section)->fetchRow($select);
          //  die(debugArray($result2));
        }
        
        // = $this->getCustomTable()->fetchRow($select2); 
        $object->fromArray($result2->toArray());

//        $object = new Content_Model_Content();
//
//        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                //->where('section = ?', $section)
//                ->where('id = ?', $id)
//                ->join(array('c' => 'content_custom_' . $section,
//                    'content_content.id = c.id AND 
//                        content_content.active_revision = c.revision')
//        ); //,'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings')
//
//        $object = new Content_Model_Content();
//
//        $result = $this->getTable()->fetchRow($select);
//
//        $object->fromArray($result->toArray());
//
        return $object;
    }

//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }

    public function addCustomTable($section) {


        $table = 'content_custom_' . mysql_real_escape_string($section);

        //@todo security issue validate $section
        // return $this->getMapper()->prepareTable($section, $fields);
        $this->getTable()->getAdapter()->query('CREATE TABLE IF NOT EXISTS `' . $table . '` (
  `id` int(11) unsigned NOT NULL,
  `revision` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner`  int(11) unsigned NOT NULL,
  `autosave`  int(1) unsigned NOT NULL,
  PRIMARY KEY (`id`,`revision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;');

        //check if table exisits
        //false
        //create table with columns
        //true
        //check to see
        ////throw Exception 

        /* CREATE TABLE IF NOT EXISTS `content_section_5` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `revision` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`,`revision`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
         */
    }

    public function addCustomField(Content_Model_Field_Interface $field) {
        //@todo check to make sure section is valid
        //check to make sure column doesn't exist yet
        //load plugin

        $class = 'Content_Plugin_Element_' . $field->element . '_Installer';

        if (!class_exists($class, true)) {
            throw new Exception('Element not found: ' . $field->element);
        }

        $element = new $class();
        $result = $this->getTable()->getAdapter()->query('ALTER TABLE `content_custom_' . mysql_real_escape_string($field->section) . '` ADD `' . mysql_real_escape_string($field->name) . '` ' . $element->getMysqlColumn() . ' ');;
        $this->cleanCache(); //reset metadata cache
        return $result;
    }
    
    public function cleanCache(){
        $cm = Zend_Registry::get('cachemanager');
        $cache = $cm->getCache('database');
        
        if(is_object($cache)){
         $cache->clean('all');
        }
    }
    

    public function renameCustomField(Content_Model_Field_Interface $old, Content_Model_Field_Interface $field) {
        //@todo check to make sure section is valid
        //check to make sure column doesn't exist yet

        if ($old->id != $field->id) {
            throw new InvalidArgumentException('Object Id\'s Do not match');
        }


        //load plugin

        $class = 'Content_Plugin_Element_' . $field->element . '_Installer';

        if (!class_exists($class, true)) {
            throw new Exception('Element not found: ' . $field->element);
        }

        $element = new $class();
        
        

        $result = $this->getTable()->getAdapter()->query('ALTER TABLE `content_custom_' . mysql_real_escape_string($field->section) . '` CHANGE `' . mysql_real_escape_string($old->name) . '` `' . mysql_real_escape_string($field->name) . '` ' . $element->getMysqlColumn() . ' ');
        $this->cleanCache(); //reset metadata cache
        return $result;
        
    }

    public function removeCustomField(Content_Model_Field_Interface $field) {
        //alter table
        $result = $this->getTable()->getAdapter()->query('ALTER TABLE `content_custom_' . mysql_real_escape_string($field->section) . '` DROP `' . mysql_real_escape_string($field->name) . '`');
        $this->cleanCache(); //reset metadata cache
        return $result;
    }

    public function removeCustomTable($section) {
        //drop table
        //DROP TABLE `content_section_5`
        $result = $this->getTable()->getAdapter()->query('DROP TABLE IF EXISTS `content_custom_' . mysql_real_escape_string($section));
        $this->cleanCache(); //reset metadata cache
        return $result;
    }

    public function save(Content_Model_Content_Interface $content) {
//die(debugArray($content)); 
        //get fields in table 1
        $table = $this->getTable();
        $fields = $table->info('cols');
        //  
        //get fields in table 2
        $customTable = $this->getCustomTable($content->section);
        $customFields = $customTable->info('cols');
        //insert into table 1
//die(debugArray($customFields)); 
        $content->revision = date('Y-m-d H:i:s');
        $content->active_revision = $content->revision;
     //   $contentValues = $content->toArray();
        $contentValues = $content->toObjects();

        if (isset($content->id)) {
            $row = $table->find($content->id)->current();
        } else {
            $row = $table->createRow();
        }
        foreach ($contentValues as $key => $value) {
            if (in_array($key, $fields)) {
                if (is_object($value)) {
                    $value = $value->getSaveValue();
                }

                $row->$key = $value;
            }
        }
        $result = $row->save();

   
        //get id
       $content->id = $row->id;
          
        //insert into table 2
        $customRow = $customTable->createRow();

        foreach ($contentValues as $key => $value) {
            if (in_array($key, $customFields)) {
                if (is_object($value)) {
                    $value = $value->getSaveValue();
                }
                if (is_array($value)) {
                    $value = serialize($value);
                }
                $customRow->$key = $value;
            }
        }
        $customRow->id = $row->id;
        $customRow->save();
        //update id in orginal content

        return $content;
    }

    public function delete(Content_Model_Content_Interface $content) {

        $table = $this->getTable();

        $customTable = $this->getCustomTable($content->section);

        //deletemain
        $row = $table->find($content->id)->current();
        if (empty($row)) {
            throw new Exception('Invalid Content');
        }
        $row->delete();

        //deletecustom
        $rowCustom = $customTable->find($content->id, $content->revision)->current();
        $rowCustom->delete();

        return true;
    }

    public function deleteById($section) {

        $customTable = $this->getCustomTable($content->section);
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);

        //deletemain
        $this->getTable()->delete($where);

        //deletecustom
        $rowCustom = $customTable->delete($where);
        return true;
    }

    public function deleteOldRevisions($id) {


        $select = $this->getTable()->select()->where('id = ?', $id)->order('revision DESC')->limit(1);
        $newest = $this->getTable()->fetchRow($select);

        $customTable = $this->getCustomTable($content->section);
        $where = $this->getTable()->getAdapter()->quoteInto('revision < ?', $newest->revision);

        //deletemain
        $this->getTable()->delete($where);

        //deletecustom
        $rowCustom = $customTable->delete($where);
        return true;
    }

    public function deleteBySection($section) {

        $where = $this->getTable()->getAdapter()->quoteInto('section = ?', $section);
        $this->getTable()->delete($where);
    }

    public function deleteAll() {

        $service = new Content_Model_Section_Service();
        $sections = $service->getObjects();

        foreach ($sections as $section) {
            $this->removeCustomTable($section->id);
        }

        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}