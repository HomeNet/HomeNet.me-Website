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
class Content_Plugin_Element_Editor_Element extends Content_Model_Plugin_Element  {

    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
     function getSetupForm($options = array()){
        $form = parent::getSetupForm();
        
        $form->setLegend('Editor Options');
        $path = $form->createElement('text','folder');
        $path->setLabel('Upload Path: ');
        $config = Zend_Registry::get('config');
        $path->setDescription('Path is Prefixed with: '.$config->site->uploadDirectory.'/');
        //$path->setRequired('true');
        $path->addFilter('StripTags');//@todo filter chars
        $form->addElement($path);
        
        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
       $element = new CMS_Form_Element_AjaxWysiwyg($config); 
        $view = Zend_Registry::get('view');
        $element->setParams($options);
        $element->setParam('rest', $view->url(array('controller'=>'content','action'=>'rest'),'content-admin'));
        return $element;
    }
    
   public function getSaveValue(){
       
   }
   
   public function getFormValue(){
       
   }
   
    
    function render(){

        $doc = new DOMDocument;
        $doc->loadHTML($this->_value);
        $xpath = new DOMXPath($doc);

        // We starts from the root element

      //  echo htmlentities($this->_value);
  
        $blocks= $xpath->evaluate('//*[@data-block]');

        foreach ($blocks as $block) {
           // echo "block $block";
          // echo "Found tag: {$block->tagName}";
          //  $block->nodeValue= 'test';
           $type = $block->getAttribute ('data-block');
           $pos = strrchr($type, '.');
           if($pos){
               $type = substr($pos,1);
           }
           
           $class = 'Content_Plugin_Block_'.ucfirst($type).'_Block';
           
           $object = new $class(array('node'=>$block));
           
           $replacement = $object->renderForm();
           if($replacement){
           
           //die(debugArray($block->parentNode));
           
          $replacement = $doc->importNode($replacement, true);

           $block->parentNode->replaceChild($replacement, $block);
           }
        }
        
        
        
        return htmlentities($doc->saveHTML());
       
        
    }
}