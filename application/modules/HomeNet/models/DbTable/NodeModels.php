<?php

class HomeNet_Model_DbTable_NodeModels extends Zend_Db_Table_Abstract
{

    protected $_name = 'homenet_node_models';

    protected $_rowClass = 'HomeNet_Model_DbTableRow_NodeModel';

//    public function fetchAllByStatus($status = 1){
//         $select = $this->select()->where('status = ?', $status)
//                 ->order(array('type','name asc'));
//        return $this->fetchAll($select);
//    }
//
//    public function  fetchRowById($id)
//    {
//        return $this->find($id)->current();
//    }
}

