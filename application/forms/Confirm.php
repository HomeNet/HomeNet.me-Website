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
 * @package Core
 * @subpackage Login
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Form_Confirm extends Zend_Form 
{

    public $label;
    
    public function __construct($label = 'Delete') {
        $this->label = $label;
        parent::__construct();
        
    }
    
    public function init()
    {
        $this->setMethod('post');
        $this->setElementDecorators( array( 'ViewHelper' ) );
        $this->addElement('submit', 'confirm', array('label' => $this->label));
        $this->addElement('submit', 'cancel', array('label' => 'Cancel'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }

}

