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
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Device_Stub_Float extends HomeNet_Model_Device_GenericSensor {


    public function getDeviceDriver(){
        return 'My'.$this->getSetting('driver').$this->position;
    }

    public function generateCustomCode(){
        return 'class HomeNetDevice'.$this->getDeviceDriver().' : public HomeNetDeviceStubFloat {
public:
    HomeNetDevice'.$this->getDeviceDriver().'(HomeNet& homeNet ):HomeNetDeviceStubFloat(homeNet) { };
    //inline uint16_t getId(){ return 0x0005; }; //uncomment if you have custom device ID

protected:
    float getValue(){
       float value = _port->anaRead();

       return value;
    }
};';
    }

}