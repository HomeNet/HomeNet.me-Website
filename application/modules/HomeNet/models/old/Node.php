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
class HomeNet_Model_Node  extends Zend_Db_Table_Row_Abstract
{
    public function importArray($array){

        if(!empty($array['id'])) {
            $this->id = $array['id'];
        }

        $this->node = $array['node'];

        if(!empty($array['model'])) {
            $this->model = $array['model'];
        }
        if(!empty($array['room'])) {
        $this->room = $array['room'];
        }
        if(!empty($array['house'])) {
        $this->house = $array['house'];
        }
        if(!empty($array['uplink'])){
            $this->uplink = $array['uplink'];
        }

        if(!empty($array['description'])) {
            $this->description = $array['description'];
        } else {
            
        }
    }

    public function addSensor(){
        //$this->model = 1;
        $this->add();
    }

    public function addInternet($ipaddress, $direction = 0){
        //$this->type = 3;
        $id = $this->add();

        $table = new HomeNet_Model_DbTable_InternetNodes();
        $row = $table->createRow();
        $row->id = $id;
        $row->status = 1;
        $row->ipaddress = $ipaddress;
        $row->direction = $direction;
        $row->save();
    }

    public function add(){

        return $this->save();
    }

    public function update(){

        return $this->save();
    }

    public function updateInternet($ipaddress, $direction = 0){
        $this->save();
    }

    public function save(){
        if(empty($this->description)){
            $this->description = '';
        }
        if(empty($this->uplink)){
            $this->uplink = null;
        }



        if(parent::save()){
            //get insert id
            
            return $this->id;
        } else {
            throw new Zend_Exception("Could not save node");
        }
    }
}

