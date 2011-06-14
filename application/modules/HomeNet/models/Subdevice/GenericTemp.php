<?php

/*
 * Arduino.php
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

/**
 * Description of Arduino
 * 
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Subdevice_GenericTemp extends HomeNet_Model_Subdevice_Generic {

    /**
     * Form for user config
     *
     * @return Zend_Form
     */
    public function getConfigForm() {

        $form = parent::getConfigForm();

        $units = $form->createElement('text', 'units');
        $units->setLabel('Units: ');
        $units->setValue($this->getSetting('units'));
        $units->addFilter('StripTags');
        $form->addElement($units);

        $convert = $form->createElement('select', 'convert');
        $convert->setLabel('Convert: ');
        $convert->setValue($this->getSetting('convert'));
$convert->setMultiOptions(array('' => 'None',
                                     'ctof' => 'C to F',
                                     'ftoc' => 'F to C'));
        $form->addElement($convert);


        return $form;
    }

    public function processConfigForm($values) {
        parent::processConfigForm($values);
        $this->setSetting('units', $values['units']);
        $this->setSetting('convert', $values['convert']);
    }

    public function saveDatapoint($value, $timestamp) {

//        if (empty($this->settings['datatype'])) {
//            throw new Zend_Exception('this subdevice doesn\'t have a datatype to save a value');
//        }



//        $class = 'HomeNet_Model_DbTable_Datapoints' . ucfirst($this->settings['datatype']);
//
//        if (!class_exists($class)) {
//            throw new Zend_Exception('Invalid Datatype: ' . $class);
//        }

        $dService = new HomeNet_Model_Datapoint_Service();
        $dService->add('Float',$this->id,$value,$timestamp);

//        $table = new $class();
//        // $table = new HomeNet_Model_DbTable_DatapointsBoolean();
//
//        $row = $table->createRow();
//
//        $row->subdevice = $this->id;
//        $row->datetime = $timestamp;
//
//        //$value = $this->_convertValue($value);
//        $row->value = $value;
//
//        $row->save();
    }
}