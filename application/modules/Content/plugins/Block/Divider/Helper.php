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
class Content_Plugin_Block_Divider_Helper extends Content_Model_Plugin_BlockHelper {

    /**
     * Get the Divider Node in the Document
     * 
     * @return DOMNode
     */
    private function getDivider(){
        
        $this->_document = $this->_element->getDocument()->cloneNode(true);
        $xpath = new DOMXPath($this->_document);

        // We starts from the root element
        //  echo htmlentities($this->_value);

        $blocks = $xpath->evaluate('//*[@data-block="cms.divider"]');
        if($blocks->length > 0){
            return $blocks->item(0);
        }
        return false;
    }
    
    private function toText(){
        $node = $this->getDivider();
        if($node === false){
            return false;
        }
        
        $text = $this->_document->createTextNode('%%CMSDIVIDER%%'); 
        
        $node->parentNode->replaceChild($text,$node);

        $body = $this->_document->getElementsByTagName('body')->item(0);

       // $html = $this->_document->saveXML($body);
        //remove body tag
        return $this->_document->saveXML($body);
    }
    
    public function exists(){
        if($this->getDivider() === false){
            return false;
        }
        return true;
    }








    public function before(){
        $old = $this->_element->getDocument();
        $text = $this->toText();
        
       // return htmlentities($text);
        //$old = $this->_document->cloneNode(true);
        
        
        if($text !== false){
            $doc = new DOMDocument;
            $parts = preg_split('/%%CMSDIVIDER%%/', $text);
            $doc->loadHTML($parts[0]);
            $this->_element->setDocument($doc);
        }
      //  die(debugArray($parts));
        
       $out = $this->_element->render();
       $this->_element->setDocument($old);
       return $out;
    }
    public function after(){
        $old = $this->_element->getDocument();
         $text = $this->toText();
        
       // return htmlentities($text);
        
        
        if($text !== false){
            $doc = new DOMDocument;
            $parts = preg_split('/%%CMSDIVIDER%%/', $text);
            $doc->loadHTML($parts[1]);
            $this->_element->setDocument($doc);
        }
      // die(debugArray($parts));
        
       $out = $this->_element->render();
       $this->_element->setDocument($old);
       return $out;
    }
    
}