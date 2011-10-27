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
class Content_Plugin_Block_Gallery_Block extends Content_Model_Plugin_Block {

public $template = '_gallery';
    
     public function prepareValues(){
//        if(empty($this->_value['path'])){
//            throw new Exception('Missing Image Path');
//        }
        //if it has id
    }
    
    public function parseNode() {
        if (empty($this->_node)) {
           return false;
        }
        
        $this->_value = $this->_getData($this->_node,array('block','title'));
        
        $images = array();
        
        foreach($this->_node->childNodes as $child){
            if($child->tagName == 'div'){
                $images[] = $this->_getData($child, array('path','name', 'title','description','source','url','copyright'));
            }
        }
        $this->_value['images'] =$images;
        
        //die(debugArray($this->_value));
    }

    public function renderView() {
        $this->prepareValues();
        $doc = new DOMDocument();
        // $frag = $doc->createDocumentFragment();
        if (empty($this->template)) {
            $block = $doc->createElement('div');
            $this->_setData($block, $this->_value);
            return $block;
        }
        //    $doc->loadHTML(debugArray($this->_value));

        $doc->loadHTML('<div data-block="' . $this->_value['block'] . '" class="cms-block-image">' . $this->_view->partial('_image.phtml', $this->_value) . '</div>');

        //  die(debugArray($this->_view->getScriptPaths()));
        //$frag->appendXML(debugArray($this->_value));
        //$frag->appendXML('<b>dsadadadsadas</b>');
        // $doc->appendChild($frag);
        return $doc->getElementsByTagName('body')->item(0)->firstChild;
        // die(htmlentities($doc->saveHTML()));
        //$doc->
        //return $frag;
    }
    
   

   public function renderForm() {
        $doc = new DOMDocument();
        $block = $doc->createElement('div');
        $this->_setData($block, $this->_value, array('block', 'title'));
        
        //get image info from db
        
        foreach ($this->_value['images'] as $image) {

            if (empty($image['path'])) {
                //image is messed up/ just skip it
                continue;
            }

            $node = $doc->createElement('div');
            $image['thumbnail'] = $this->_view->image($image['path'], '', 100, 75);
            $image['preview'] = $this->_view->image($image['path'], '', 480, 320);

            $this->_setData($node, $image, array('id', 'path', 'thumbnail', 'preview', 'title', 'description', 'copyright', 'source', 'url', 'copyright',
                'owner', 'fullname', 'width', 'height'));
            $block->appendChild($node);
        }

        return $block;
    }

    public function renderSave() {

        $doc = new DOMDocument();
        $block = $doc->createElement('div');
        $this->_setData($block, $this->_value, array('block', 'title'));
        foreach ($this->_value['images'] as $image) {
            $node = $doc->createElement('div');
            $this->_setData($node, $image, array('id', 'filename', 'path', 'title', 'description', 'copyright', 'source', 'url', 'copyright'));
            $block->appendChild($node);
        }

        return $block;
    }
    
}