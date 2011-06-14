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
 * @subpackage Alert
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_Alerts extends Zend_Db_Table_Abstract
{

    protected $_name = 'homenet_alerts';

    protected $_rowClass = 'HomeNet_Model_Alert';

    public function fetchAllByUser($user){

       $select = $this->select()->where('user = ?',$user);

       return $this->fetchAll($select);
    }

     public function fetchAllByHouseOrUser($house, $user){
       $houses = array();
       if(!is_array($house))
       {
           $houses[] = $house;
       } else {
           $houses = $house;
       }

       $select = $this->select()->where('user = ?',$user)
               ->orWhere('house in (?)', $houses)
               ->order('id DESC')
               ->limit(20);

       return $this->fetchAll($select);
    }


     public function add($level,$message,$user = null, $house =null,$room = null,$subdevice = null){
        $row = $this->createRow();
        $row->level = $level;
        $row->message = $message;
        $row->user = $user;
        $row->house = $house;
        $row->room = $room;
        $row->subdevice = $subdevice;
        $row->save();
    }


}

