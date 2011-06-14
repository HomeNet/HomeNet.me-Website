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
 * @subpackage Packet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Form_Packet extends Zend_Form
{

    public function init()
    {

        $this->setMethod('post');

        $sub = new Zend_Form_SubForm();
        $sub->setLegend('Send Packet');


        $fromNode = $this->createElement('text','fromNode');
        $fromNode->setLabel('From Node: ');
        $fromNode->setValue(4095);
        $fromNode->setRequired('true');
        $fromNode->addFilter('Digits');
        $fromNode->addValidator('Between', false, array('min' => 0, 'max' => 4095));
        $sub->addElement($fromNode);

        $fromDevice = $this->createElement('text','fromDevice');
        $fromDevice->setLabel('From Device: ');
        $fromDevice->setValue(0);
        $fromDevice->setRequired('true');
        $fromDevice->addFilter('Digits');
        $fromDevice->addValidator('Between', false, array('min' => 0, 'max' => 16));
        $sub->addElement($fromDevice);

        $toNode = $this->createElement('text','toNode');
        $toNode->setLabel('To Node: ');
        $toNode->setValue(1);
        $toNode->setRequired('true');
        $toNode->addFilter('Digits');
        $toNode->addValidator('Between', false, array('min' => 0, 'max' => 4095));
        $sub->addElement($toNode);

        $toDevice = $this->createElement('text','toDevice');
        $toDevice->setLabel('To Device: ');
        $toDevice->setValue(0);
        $toDevice->setRequired('true');
        $toDevice->addFilter('Digits');
        $toDevice->addValidator('Between', false, array('min' => 0, 'max' => 16));
        $sub->addElement($toDevice);


        $commands = include(dirname(__FILE__).'/../data/commands.php');

        $command = $this->createElement('select', 'command');
        $command->setLabel("Command:");
        $command->setRequired('true');
        foreach($commands as $key => $value){
            $command->addMultiOption($key.'|'.$value['type'], $value['name']);
        }
        
        
        $sub->addElement($command);

        $payload = $this->createElement('text','payload');
        $payload->setLabel('Payload: ');
        $sub->addElement($payload);




        $this->addSubForm($sub, 'packet');

        $submit = $this->addElement('submit', 'submit', array('label' => 'Send'));



    }


}

