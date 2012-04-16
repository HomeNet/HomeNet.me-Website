<?php
/* 
 * Jeenode.php
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
 * Description of Jeenode
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Plugin_Network_Rfm12b_Network extends HomeNet_Model_Network_Abstract {
    
    public function getSetupForm() {
        $form = parent::getSetupForm();
        
        $channel = $form->createElement('text', 'channel');
        $channel->setLabel('Channel');
        $channel->addFilter('int');
        $channel->addValidator('greaterThan', true, array('min'=>0));
        $channel->addValidator('lessThan', true, array('max'=>128));
        $form->addElement($channel);
        
        return $form;
    }

}