<?php

/*
 * Interface.php
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
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Block_Html_Block  extends Content_Model_Plugin_Block  {

       public $template = '_html';
       
      public function parseNode() {
          
        return false;  
          
        if (empty($this->_node)) {
            $this->_value['content'] = '';
        }
        
        $doc = new DOMDocument();
        $doc->importNode($this->_node, true);
        $doc->appendChild($this->_node);
        
        
        
        
        $this->_value['content'] = $doc->saveHTML();
        
        die(debugArray($this->_value));
    }  
    
    public function getViewNode() {
        
        return false;
        
        $this->prepareValues();
        $doc = new DOMDocument();
        // $frag = $doc->createDocumentFragment();
        if (empty($this->template)) {
            $block = $doc->createElement('div');
            $this->_setData($block, $this->_value);
            return $block;
        }
        //    $doc->loadHTML(debugArray($this->_value));

        $doc->loadHTML('<div data-block="' . $this->_value['block'] . '" class="cms-block-html">' . 
                $this->_view->partial($this->template.'.phtml', $this->_value) .
                '</div>');

        return $doc->getElementsByTagName('body')->item(0)->firstChild;
    }
    
   

    public function getFormNode() {
        
        return self::IGNORE_NODE;
//        $this->prepareValues();
//        $doc = new DOMDocument();
//        $block = $doc->createElement('div');
//       
//        $this->_value['thumbnail'] = $this->_view->image($this->_value['path'], '', 100, 75);
//        $this->_value['preview'] = $this->_view->image($this->_value['path'], '', 480, 320);
//
//        //get image info from db
//        
//        $this->_setData($block, $this->_value, array('id', 'path', 'thumbnail', 'preview', 'title', 'description', 'copyright', 'source', 'url', 'copyright',
//            'owner','fullname','width','height'));

        return $this->_node;
    }

    public function getSaveNode() {
        
        return self::IGNORE_NODE;
        
//        $this->prepareValues();
//        $doc = new DOMDocument();
//        $block = $doc->createElement('div');
//        $this->_setData($block, $this->_value, array('id', 'path', 'title', 'description', 'copyright', 'source', 'url', 'copyright'));
//
//        return $block;
        return $this->_node;
    }
}