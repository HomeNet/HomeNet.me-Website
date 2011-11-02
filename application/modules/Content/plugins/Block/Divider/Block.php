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
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Block_Divider_Block extends Content_Model_Plugin_Block {

//public $template = '_gallery';
    
     public function prepareValues(){
//        if(empty($this->_value['path'])){
//            throw new Exception('Missing Image Path');
//        }
        //if it has id
    }
    
    public function parseNode() {
//        if (empty($this->_node)) {
//           return false;
//        }
//        
//        $this->_value = $this->_getData($this->_node,array('block','title'));
//        
//        $images = array();
//        
//        foreach($this->_node->childNodes as $child){
//            if($child->tagName == 'div'){
//                $images[] = $this->_getData($child, array('path','name', 'title','description','source','url','copyright'));
//            }
//        }
//        $this->_value['images'] =$images;
        
        //die(debugArray($this->_value));
    }

    public function getViewNode() {
       return self::IGNORE_NODE;
    }
    
   

   public function getFormNode() {
       return self::IGNORE_NODE;
    }

    public function getSaveNode() {
        return self::IGNORE_NODE;
        $doc = new DOMDocument();
        $block = $doc->createElement('div');
        $this->_setData($block, $this->_value, array('block'));
       
        return $block;
    }
    
    
    
}