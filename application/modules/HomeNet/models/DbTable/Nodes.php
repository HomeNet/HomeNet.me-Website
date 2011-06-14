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
 * @package HomeNet
 * @subpackage Node
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_Nodes extends Zend_Db_Table_Abstract
{

    protected $_name = 'homenet_nodes';

    protected $_rowClass = 'HomeNet_Model_DbTableRow_Node';

//    protected $_rowClass = 'HomeNet_Model_Node';
//
//    public function fetchRowById($id){
//
//        return $this->find($id)->current();
//    }
//
//    public function fetchAllByHouse($id){
//
//        $select = $this->select()->where('house = ?', $id);
//        return $this->fetchAll($select);
//    }
//
//    public function fetchAllByHouseWithModel($house){
//
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//               ->where('house = ?', $house)
//               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model',array('name'));
//
//        return $this->fetchAll($select);
//    }
//
//    public function fetchAllByRoom($id){
//
//        $select = $this->select()->where('room = ?', $id);
//        return $this->fetchAll($select);
//    }
//
//    public function fetchNextId($house){
//
//        $select = $this->select()->where('house = ?', $house)
//                                  ->where('node NOT IN(?)', array(255,4095))
//                                  ->order('node DESC')
//                                  ->limit(1);
//       $row = $this->fetchRow($select);
//        $next = $row['node'] + 1;
//        return $next;
//    }
//
//    public function fetchAllInternetNodes($house){
//
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//               ->where('homenet_nodes.house = ?', $house)
//               ->join('homenet_nodes_internet', 'homenet_nodes.id = homenet_nodes_internet.id');
//               // ->limit(1);
//
//        return $this->fetchAll($select);
//    }
//    /*
//    public function fetchRowHouseNode($house,$node){
//
//        $select = $this->select()->where('house = ?', $house)
//                                 ->where('node = ?', $node)
//                                 ->order('node DESC');
//        return $this->fetchAll($select);
//    }*/
//    //
//
//    public function fetchRowByIdWithModel($id, $columns = array('name','driver', 'max_devices')) {
//
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//               ->where('homenet_nodes.id = ?', $id)
//               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model',$columns)
//                ->limit(1);
//
//        return $this->fetchRow($select);
//    }
//    /**
//     *
//     * @param int $id
//     * @return HomeNet_Model_Node_Abstract
//     */
//     public function fetchDriverById($id) {
//
//       $row = $this->fetchRowByIdWithModel($id,array('driver','settings'));
//
//        if(is_null($row)){
//            throw new Zend_Exception('Node model '.$id.' doesn\'t exist');
//        }
//
//        if (!class_exists($row->driver)) {
//            throw new Zend_Exception('Node driver ' . $row->driver . ' not found');
//        }
//
//        return new $row->driver($row);
//    }
//
//    /**
//     *
//     * @param int $id
//     * @return HomeNet_Model_Node_Abstract
//     */
//     public function fetchInternetDriverById($id) {
//
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//               ->where('homenet_nodes.id = ?', $id)
//               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model',array('driver','settings'))
//               ->join('homenet_nodes_internet', 'homenet_nodes_internet.id = homenet_nodes.id')
//               ->limit(1);
//
//        $row = $this->fetchRow($select);
//
//        //die(debugArray($row->toArray()));
//
//        if(empty($row)){
//            throw new Zend_Exception('Node '.$id.' doesn\'t exist');
//        }
//
//        if (!class_exists($row->driver)) {
//            throw new Zend_Exception('Node driver ' . $row->driver . ' not found');
//        }
//
//        return new $row->driver($row);
//    }



}