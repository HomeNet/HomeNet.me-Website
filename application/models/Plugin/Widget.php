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
 * Description of Widget
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Core_Model_Plugin_Widget {
    /**
     * @var array widget options
     */
    private $_options;
    
    /**
     * Zend View object to use with rendering
     * 
     * @var Zend_View
     */
    private $_view;
    
    public function __construct($config) {
        if (isset($config['options'])) {
            $this->_options = $config['options'];
        }

        if (isset($config['view'])) {
            if (!($config['view'] instanceof Zend_View)) {
                throw new InvalidArgumentException('Invalid Zend View Supplied');
            }

            $this->_view = $config['view'];
        } else {
            $this->_view = Zend_Registry::get('view');
        }
    }
    
    public function render(){
        return '';
    }
}

?>
