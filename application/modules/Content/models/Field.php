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
 * @package Content
 * @subpackage Section
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Field implements Content_Model_Field_Interface {

    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $section;
    
    /**
     * @var order
     */
    public $order = 0;
    
    /**
     * @var string
     */
    public $type = 2;
    
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $label;
     /**
     * @var string
     */
    public $description ='';
    /**
     * @var string
     */
    public $value = '';
    
    /**
     * @var array
     */
    public $attributes = array();
    
    /**
     * @var array
     */
    public $validators = array();
    /**
     * @var array
     */
    public $filters = array();
    /**
     * @var boolean
     */
    public $locked = false;
    
    public $element;
    
    public $required = false;
    
    public $visible = true;
    
    const SYSTEM = 0;
    const TEMPLATE = 1;
  const USER = 2;

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
    }

    public function fromArray(array $array) {

        $vars = get_object_vars($this);

        // die(debugArray($vars));

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $vars)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        return get_object_vars($this);
    }

}