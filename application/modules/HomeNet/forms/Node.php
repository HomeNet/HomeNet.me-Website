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
class HomeNet_Form_Node extends CMS_Form {
    
    private $type;
    private $uplinkNodeIds;
    private $house;
    
    public function __construct($modelType = null, $house = null ) {
        if($house === null){
            throw new InvalidArgumentException('Missing House Id');
        }
        
        
        $this->type = $modelType;
        $this->house = $house;
        parent::__construct();
    }

    public function init() {
        $this->setMethod('post');

        //get all models
        $nService = new HomeNet_Model_Node_Service();
        $types = $nService->getTypes();

        $nmService = new HomeNet_Model_NodeModel_Service();
        $objects = $nmService->getObjectsByStatus(1);

        // die(debugArray($models));

        $models = array();

        if ($this->type === null) {
            foreach ($objects as $value) {
               $models[$types[$value->type]][$value->id] = $value->name;
            }
        } else {
            foreach ($objects as $value) {
                if ($value->type == $this->type) {
                    $models[$types[$value->type]][$value->id] = $value->name;
                }
            }
        }

        $model = $this->createElement('select', 'model');
        $model->setLabel("Model: ");
        $model->addMultiOptions($models);
        $model->setRequired();
        $this->addElement($model);

        $id = $this->createElement('text', 'address');
        $id->setLabel('Address: ');
        $id->setValue($this->id);
        $id->setRequired(true);
        $id->addFilter('Digits');
        $id->addValidator('Between', false, array('min' => 0, 'max' => 4095));
        $this->addElement($id);

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows', '3');
        $description->setAttrib('cols', '20');
        $this->addElement($description);
        
        $nodeService = new HomeNet_Model_Node_Service;
        $uplinks = $nodeService->getUplinksByHouse($this->house);

        $uplink = $this->createElement('select', 'uplink');
        $uplink->setLabel("Uplink: ");
        $uplink->addMultiOptions($uplinks);
        $this->addElement($uplink);
        
        $house = HomeNet_Model_House_Manager::getHouseById($this->house);
        $rooms = $house->getRooms();
        $regions = $house->getRegions();
        $roomList = array_fill_keys(array_keys(array_flip($regions)), array());
        
        foreach($rooms as $room){
           // $room->region;
           // $room->id;
          //  $room->name;
            $roomList[$regions[$room->region]][$room->id] = $room->name;
        }        
        
        $room = $this->createElement('select', 'room');
        $room->setLabel("Room: ");
        $room->addMultiOptions($roomList);
        $this->addElement($room);

        //$this->addSubForm($this, 'node');

        $this->addDisplayGroup($this->getElements(), 'node2', array('legend' => 'Node'));
    }

}

