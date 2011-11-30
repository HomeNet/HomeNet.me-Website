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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Plugin_Device_StubLong_Device extends HomeNet_Model_Device_Abstract {

    public function getSchedule() {
        $default = array(0, 60, $this->getDeviceVariable(), 4095, 0, HomeNet_Model_Packet::GETVALUE, '""');
        return array($default);
    }

    public function getDeviceDriver() {
        return 'My' . $this->getSetting('driver') . $this->position;
    }

    public function generateCustomCode() {
        return 'class  HomeNetDevice' . $this->getDeviceDriver() . ' : public HomeNetDeviceStubLong {
public:
    HomeNetDevice' . $this->getDeviceDriver() . '(HomeNet& homeNet ):HomeNetDeviceStubLong(homeNet) { };
    //inline uint16_t getId(){ return 0x0005; }; //uncomment if you have custom device ID

protected:
    long getValue(){
       long value = _port->anaRead();

       return value;
    }
};';
    }

}