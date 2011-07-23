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
class Core_Model_Acl_Group_MapperDbTable implements Core_Model_Acl_Group_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Core_Model_DbTable_Acl_Group;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Core_Model_DbTable_GroupAcls();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id){
        return $this->getTable()->find($id)->current();
    }
    
    public function fetchObjectsByGroup($group){
       $select = $this->getTable()->select()
               ->where('`group` = ?',$group)
               ->where('object is NULL');
      return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByGroups(array $groups){
       $select = $this->getTable()->select()
               ->where('`group` in (?)',$groups)
                ->where('object is NULL');
      return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByGroupsModule(array $groups, $module) {
        $select = $this->getTable()->select()
        ->where('`group` in (?)', $groups)
        ->where('module = ?', $module)
        ->order(array('controller','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByGroupsModuleController($groups, $module, $controller) {
        $select = $this->getTable()->select()
        ->where('`group` in (?)', $groups)
        ->where('module = ?', $module)
        ->where('controller = ?', $controller)
        ->where('object = ?', null)
        ->order(array('controller','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByGroupsModuleControllerObject($groups, $module, $controller, $object) {
        $select = $this->getTable()->select()
        ->where('`group` in (?)', $groups)
        ->where('module = ?', $module)
        ->where('controller = ?', $controller)
        ->where('object = ?', $object)
        ->order(array('controller','object','action'));
        
        return $this->getTable()->fetchAll($select);
    }
    
    public function fetchObjectsByGroupsModuleControllerObjects($groups, $module, $controller, $objects) {
        $select = $this->getTable()->select()
        ->where('`group` in (?)', $groups)
        ->where('module = ?', $module)
        ->where('controller = ?', $controller)
        ->where('object in (?)', $objects)
        ->order(array('controller','object','action'));
        
        return $this->getTable()->fetchAll($select);
    }

    public function save(Core_Model_Acl_Group_Interface $membership) {

        if (($membership instanceof Core_Model_DbTableRow_GroupAcl) && ($membership->isConnected())) {
            return $membership->save();

        } elseif (!is_null($membership->id)) {
            $row = $this->getTable()->find($membership->id)->current();
            if(empty($row)){
               $row = $this->getTable()->createRow();
            }

        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($membership->toArray());
       // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(Core_Model_Acl_Group_Interface $membership) {

        if (($membership instanceof Core_Model_DbTableRow_GroupAcl) && ($membership->isConnected())) {
            $membership->delete();
            return true;
        } elseif (!is_null($membership->id)) {
            $row = $this->getTable()->find($membership->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Group Object');
    }
    
    public function deleteByGroup($group){
         $select = $this->getTable()->select()->where('group = ?',$group);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV == 'testing'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}