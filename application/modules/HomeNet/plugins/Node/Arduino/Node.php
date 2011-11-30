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
class HomeNet_Plugin_Node_Arduino_Node extends HomeNet_Model_Node_Abstract {
    
    
    public function canGenerateCode() {
     return true;
    }
    

    /**
     * Generate code for the node
     *
     * @return string
     */
    public function getCode() {
        $code = parent::getCode();
        $code .= '
#include <Ports.h>
#include <RF12.h>
#include <HomeNet.h>
#include <HomeNetDevices.h>
' . $this->getIncludesCode() . '

' . $this->getCustomCode() . '

//Start HomeNet packet stack
HomeNet stack(' . $this->node . ');//0x01 is RF12 base station //0xFF is PC uplink

//Setup network adapters
' . $this->getPortsCode() . '
//Setup attached devices
' . $this->getDeviceDriverCode() . '
//Package the setup info in a nice neat arrays
' . $this->getInitCode() . '

' . $this->getDeviceScheduleCode() . '

' . $this->getDeviceInterruptsCode() . '
void setup() {
  //Initialize HomeNet with the setup info
' . $this->getSetupCode() . '
}

void loop() {
  stack.loop();
  ' . $this->getLoopCode() . '
}
';

        return $code;
    }

    public function getIncludesCode() {
        $includes = array();
        $devices = $this->getDevices();
        
        foreach ($devices as $position => $device) {
            $includes = array_merge($includes, $device->getIncludes());
        }
        
        $code = '';

        foreach($includes as $value){
            $code .= "#include <$value.h>";
        }
        return $code;
    }

    public function getCustomCode(){
         $custom = array();
        $devices = $this->getDevices();

        foreach ($devices as $position => $device) {
            if(empty($custom[$device->getDeviceDriver()])){
                $custom[$device->getDeviceDriver()] = $device->generateCustomCode();
            }
        }

       
        return implode("\n",$custom);

    }

    public function getPortsCode() {

        $ports = '';

        if (!empty($this->settings['serial'])) {
            $ports .= 'HomeNetPortSerial portSerial(stack, PORT_SERIAL);'."\n";
        }

        if (!empty($this->settings['rf12b'])) {

            $freq = 915;
            if (!empty($this->settings['rf12b_freq'])) {
                $freq = $this->settings['rf12b_freq'];
            }

            $ports .= 'HomeNetPortRF12   portRF12(stack, SEND_RECEIVE, RF12_' . $freq . 'MHZ, 33);'."\n";
        }

        return $ports;
    }

    public function getNodeDriver() {
        return 'Arduino';
    }

    private $_deviceVariables = array();
    private $_statusLights = null;
    private $_hasSchedule = false;
    private $_hasInterrupts = false;

    public function getDeviceDriverCode() {

        $this->_deviceVariables = array();
        $this->_deviceVariables[0] = strtolower($this->getNodeDriver()) . '0';
        $code = array();

        $code[0] = 'HomeNetDevice' . $this->getNodeDriver() . ' ' . strtolower($this->getNodeDriver()) . "0(stack);";

        $devices = $this->getDevices();
        foreach ($devices as $position => $device) {

            $driver = $device->getDeviceDriver();

            if (!is_null($driver)) {
                $this->_deviceVariables[$position] = $device->getDeviceVariable();

                if ($driver == 'StatusLights') {
                    $this->_statusLights = $device->getDeviceVariable();
                }

                $code[$position] = 'HomeNetDevice' . $driver . ' ' . $device->getDeviceVariable() . '(' . $device->getDeviceOptions() .");";
            } else {
                $code[$position] = "//Custom driver for device $position \n";
            }
        }
        
        for($i = 0; $i < count($code); $i++){
            if(empty($code[$i])){
                $this->_deviceVariables[$i] = 'placeholder'.$i;
                $code[$i] = 'HomeNetDevicePlaceholder placeholder'.$i.'(stack);';
            }
        }

        ksort($this->_deviceVariables);
        ksort($code);
        
        return implode("\n",$code);
    }

    public function getInitCode() {
        $ports = array();

        if (!empty($this->settings['serial'])) {
            $ports[] = '&portSerial';
        }

        if (!empty($this->settings['rf12b'])) {
            $ports[] = '&portRF12';
        }
        $devices = array();
        foreach ($this->_deviceVariables as $value) {
            $devices[] = '&' . $value;
        }

       // die(debugArray($this->_deviceVariables));


        return 'HomeNetPort * ports[] = {' . implode(', ', $ports) . '};
HomeNetDevice * devices[] = {' . implode(', ', $devices) . '};';
    }

    public function getDeviceScheduleCode() {

        $schedule = array();
        $devices = $this->getDevices();

        foreach ($devices as $position => $device) {
            $schedule = array_merge($schedule, $device->getSchedule());
        }
        if (empty($schedule)) {
            return '';
        }

        $this->_hasSchedule = true;

        $code = "//Scheduled Packets
//delay (sec), frequency (sec), device, sendToNode, sendToDevice, Command, Payload\n";

        foreach ($schedule as $key => $value) {
            $value[2] = '&'.$value[2];
            $schedule[$key] = '{' . implode(',', $value) . '}';
        }

        $code = 'HomeNetSchedule schedule[] = {' . implode(",\n", $schedule) . '};';
        /* HomeNetSchedule schedule[] = {{0,8,&led,255,0,CMD_ON, 0},
          {2,8,&led,255,0,CMD_OFF,0},
          {4,8,&led,255,0,CMD_LEVEL,Payload((uint8_t) 20)},
          {6,8,&led,255,0,CMD_LEVEL,Payload((uint8_t) 120)}}; */



        return $code;
    }

    public function getDeviceInterruptsCode() {

        $interrupts = array();
        $devices = $this->getDevices();
        foreach ($devices as $position => $device) {
            $interrupts = array_merge($interrupts, $device->getInterrupts());
        }
        if (empty($interrupts)) {
            return '';
        }

        $this->_hasInterrupts = true;

        $code = "//Interrupt Packets
//device, sendToNode, sendToDevice, Command, Payload\n";

        foreach ($interrupts as $key => $value) {
            $interrupts[$key] = '{&' . implode(',', $value) . '}';
        }

        $code = 'HomeNetInterrupt interrupt[] = {' . implode(",\n", $interrupts) . '};';
        return $code;
    }

    public function getOtherCode() {
        return '';
    }

    public function getSetupCode() {
        $setup = '  stack.init(ports, sizeof(ports)/2, devices, sizeof(devices)/2);' . "\n";

        if (in_array('StatusLights', $this->_deviceVariables)) {
            $setup .= '  stack.registerStatusLights(statusLights); //setup status lights' . "\n";
        }

        if ($this->_hasSchedule) {
            $setup .= '  stack.registerSchedule(schedule,sizeof(schedule)/sizeof(schedule[0]));' . "\n";
        }

        if ($this->_hasInterrupts) {
            $setup .= '  stack.registerInterrupts(interrupts,sizeof(interrupts)/sizeof(interrupts[0]));' . "\n";
        }

        if (!empty($this->_statusLights)) {
            $setup .= '  stack.registerStatusLights('.$this->_statusLights.");\n";
        }

        return $setup;
    }

    public function getLoopCode() {
        return '';
    }

    /**
     * get max number of ports
     *
     * @return int
     */
    public function getMaxPorts() {
        return 4;
    }

}