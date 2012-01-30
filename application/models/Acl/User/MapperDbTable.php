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
 * @package Core
 * @subpackage Acl_User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Acl_User_MapperDbTable implements Core_Model_Acl_User_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Core_Model_DbTable_Acl_User;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('user_acls');
            $this->_table->setRowClass('Core_Model_Acl_User_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }


    /*
     * 
     */

    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }
    
    public function fetchObjectsByUser($user){
       $select = $this->getTable()->select()->where('user = ?',$user)
               ->where('collection is NULL')
               ->where('object is NULL');
      return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByUserModule($user, $module) {
        $select = $this->getTable()->select()
        ->where('user = ?', $user)
        ->where('(module = ? OR module is NULL)', $module) //->orWhere('module is NULL')
        ->where('collection is NULL')
        ->where('object is NULL')
        ->order(array('controller','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByUserModuleCollection($user, $module, $collection) {
        $select = $this->getTable()->select()
        ->where('user = ?', $user)
        ->where('module = ?', $module)        
        ->where('collection = ?', $collection)
        ->order(array('controller','object','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
     public function fetchObjectsByUserModuleControllerObject($user, $module, $controller, $object) {
        $select = $this->getTable()->select()
        ->where('user = ?', $user)
        ->where('module = ?', $module)
        ->where('controller = ?', $controller)        
        ->where('object = ?', $object)
        ->order(array('controller','object','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    
    public function fetchObjectsByUserModuleControllerObjects($user, $module, $controller, $objects) {
        $select = $this->getTable()->select()
        ->where('user = ?', $user)
        ->where('module = ?', $module)
        ->where('controller = ?', $controller)            
        ->where('object in (?) ', $objects)
        ->order(array('controller','object','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    

    public function save(Core_Model_Acl_User_Interface $object) {

        if (($object instanceof Core_Model_DbTableRow_UserAcl) && ($object->isConnected())) {
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
        return $row->save();

        //return $row;
    }

    public function delete(Core_Model_Acl_User_Interface $object) {

        if (($object instanceof Core_Model_DbTableRow_UserAcl) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid User Object');
    }
    
     public function deleteByUser($user){
         $where = $this->getTable()->getAdapter()->quoteInto('user = ?',$user);
         return $this->getTable()->delete($where);
    }
    
     public function deleteByModule($module) {
         $where = $this->getTable()->getAdapter()->quoteInto('module = ?',$module);
         return $this->getTable()->delete($where);
    }
    
    public function deleteByModuleCollection($module, $collection) {
        $where = array();
        $where[] = $this->getTable()->getAdapter()->quoteInto('module = ?',$module);
        $where[] = $this->getTable()->getAdapter()->quoteInto('collection = ?',$collection);

        return $this->getTable()->delete($where);
    }
    
    public function deleteByUserModuleCollection($user, $module, $collection) {
        $where = array();
        $where[] = $this->getTable()->getAdapter()->quoteInto('user = ?',$user);
        $where[] = $this->getTable()->getAdapter()->quoteInto('module = ?',$module);
        $where[] = $this->getTable()->getAdapter()->quoteInto('collection = ?',$collection);

        return $this->getTable()->delete($where);
    }
    
    public function deleteByModuleControllerObject($module, $controller, $object) {
        
        $where = array();
        $where[] = $this->getTable()->getAdapter()->quoteInto('module = ?',$module);
        $where[] = $this->getTable()->getAdapter()->quoteInto('controller = ?',$controller);
        $where[] = $this->getTable()->getAdapter()->quoteInto('object = ?',$object);

        return $this->getTable()->delete($where);
    }
    
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}