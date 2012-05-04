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
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Form_NetworkTypes extends CMS_Form
{

    public function init()
    {
        $service = new HomeNet_Model_NetworkType_Service();
        
        $objects = $service->getObjectsByStatus(HomeNet_Model_NetworkType::LIVE);
        $array = array();
        foreach($objects as $object){
            $array[$object->id] = $object->name;
        }
        
       // var_dump($array);
        
        $this->setMethod('post');

        

        $type = $this->createElement('radio', 'network');
        $type->setLabel("Primary Network");
        $type->setRequired('true');
        
        $type->setMultiOptions($array);


        $this->addElement($type);


        $this->addDisplayGroup($this->getElements(), 'networktypes', array('legend' => 'Home'));        
    }

}

