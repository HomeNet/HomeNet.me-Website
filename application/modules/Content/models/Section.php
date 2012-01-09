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
class Content_Model_Section implements Content_Model_Section_Interface {

    /**
     * @var int
     */
    public $id;
    
    /**
     * @var string
     */
    public $package;

    /**
     * @var string
     */
    public $url;
    
    /**
     * @var string
     */
    public $title;
    
    public $title_label = null;
    
    /**
     * @var string
     */
    public $description = null;
    
    /**
     * @var int
     */
    public $visible = false;
    
    public $_fields = null;
  

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
    
    public function getFields(){
        
        if(!isset($this->id)){
            throw new Exception('Id Not Loaded yet');
        }
        
        if($this->_fields === null){
            $service = new Content_Model_Field_Service();
            $this->_fields = $service->getMetadataBySection($this->id);
        }
        return $this->_fields;
    }
    
    public function getField($name){
        $fields = $this->getFields();
        if(!empty($fields[$name])){
            return $fields[$name];
        }
        
        return null;
    }
    

}