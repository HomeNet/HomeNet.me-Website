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
 * @subpackage Apikey
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_Apikeys extends Zend_Db_Table_Abstract
{

    protected $_name = 'homenet_apikeys';

    protected $_rowClass = 'HomeNet_Model_DbTableRow_Apikey';

//     public function fetchRowById($id){
//
//       return $this->find($id)->current();
//    }
//
//     public function fetchAllByHouseUser($house,$user = null){
//       if(empty($house)){
//           throw new Zend_Exception("House Required");
//       }
//       if(is_null($user)){
//           $u = new Zend_Session_Namespace('User');
//           $user = $u->id;
//        }
//
//       $select = $this->select()->where('user = ?',$user)
//                                ->where('house = ?',$house);
//
//       return $this->fetchAll($select);
//    }
//
//    public function validate($key,$house = null){
//       $count= 0;
//        if(!preg_match('/\b([a-f0-9]{40})\b/', $key)){
//            //return false;
//            throw new Zend_Exception('Invalid Api Key Format');
//        }
//
//        if(!is_null($house)){
//            $select = $this->select()->where('id = ?',$key)
//                                    ->where('house = ?',$house);
//            $row = $this->fetchAll($select);
//
//
//        } else {
//            $row = $this->find($key);
//        }
//
//        $count = $row->count();
//
//        if($count == 0) {
//           throw new Zend_Exception('Invalid API Key');
//
//            //return false
//
//        }
//        return $row->current();
//
//    }



}

