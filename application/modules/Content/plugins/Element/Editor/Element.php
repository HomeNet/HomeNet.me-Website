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
class Content_Plugin_Element_Editor_Element extends Content_Model_Plugin_Element {

    
    
    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
      
    public function getSetupForm($options = array()) {
        $form = parent::getSetupForm();

        $form->setLegend('Editor Options');
        $path = $form->createElement('text', 'folder');
        $path->setLabel('Upload Path: ');
        $config = Zend_Registry::get('config');
        $path->setDescription('Path is Prefixed with: ' . $config->site->uploadDirectory . DIRECTORY_SEPARATOR);
        //$path->setRequired('true');
        $path->addFilter('StripTags'); //@todo filter chars
        $path->addFilter('Callback', array('callback' => 'cleanDir'));
        $path->addValidator('Callback', false, array('callback' => array($this, 'validateUploadPath')));
       // 
        $form->addElement($path);

        return $form;
    }
    
    public function validateUploadPath($value) {
        $config = Zend_Registry::get('config');
        $fullPath = $config->site->uploadDirectory . DIRECTORY_SEPARATOR . $value;
       // is_dir()
        
       // if (!file_exists($fullPath)){
        if (!file_exists($fullPath)){
            if (mkdir($fullPath, 0777, true)) {
                return true;
            }
            return false;
        }
        return true;
    }


    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()) {
        
        $element = new CMS_Form_Element_JsWysiwyg($config);
        $view = Zend_Registry::get('view');
        $element->setParams($options);
        $element->setParam('url', $view->url(array('controller' => 'content', 'action' => 'rest'), 'content-admin'));
        return $element;
    }

    public function getSaveValue() {
        return str_replace(chr(13), '', $this->_processBlocks('save')); //strip carrage returns out
    }

    public function getFormValue() {
        return $this->_processBlocks('form');
    }

    function render() {
        return str_replace('&#13;', '', $this->_processBlocks('view'));
    }

    private function _processBlocks($render = 'view') {
        
        if(empty($this->_value)){
            return '';
        }
        
       // if (APPLICATION_ENV == 'production') {
            libxml_use_internal_errors(true);
       // }
        $doc = $this->getDocument();
        $xpath = new DOMXPath($doc);

        // We starts from the root element
        //  echo htmlentities($this->_value);

        $blocks = $xpath->evaluate('//*[@data-block]');

        foreach ($blocks as $block) {
            // echo "block $block";
            // echo "Found tag: {$block->tagName}";
            //  $block->nodeValue= 'test';
            $type = $block->getAttribute('data-block');
            $pos = strrchr($type, '.');
            if ($pos) {
                $type = substr($pos, 1);
            }

            $class = 'Content_Plugin_Block_' . ucfirst($type) . '_Block';

            $object = new $class(array('node' => $block));

            switch ($render) {
                case "form":
                    $replacement = $object->getFormNode();
                    break;

                case "save":
                    $replacement = $object->getSaveNode();
                    break;
                case "view":
                default:
                    $replacement = $object->getViewNode();
                    break;
            }
            
            if ($replacement === Content_Model_Plugin_Block::REMOVE_NODE) {
                $block->parentNode->removeChild($block);
            } elseif ($replacement instanceof DOMNode) {

                //die(debugArray($block->parentNode));
                $replacement = $doc->importNode($replacement, true);

                $block->parentNode->replaceChild($replacement, $block);
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        
       // $body->
        
        $html = $doc->saveHTML();
        $html = substr($html, 119, strlen($html) - 134);
        return $html;
       // die(htmlentities($html));
        //remove body tag
        //return substr($html, 6, strlen($html) - 13);
    }
    
    private $_helpers = array();
    private $_document;
    
    public function setValue($value){
        parent::setValue($value);
        $this->_document = null; //force rebuild of document
    }
    
    public function getDocument(){
        if(empty($this->_document)){
            $this->_document = new DOMDocument;
            if(!empty($this->_value)){
              libxml_use_internal_errors(true); //supress errors caused by html5

                $this->_document->loadHTML($this->_value);
            }
        }
        return $this->_document;
    }
    
    public function setDocument(DOMDocument $doc){
        
        return $this->_document = $doc;
    }
    
    public function block($block){
        if(!array_key_exists($block, $this->_helpers)){
            $class = 'Content_Plugin_Block_'.  ucfirst($block).'_Helper';
            if(class_exists($class, true)){
                $this->_helpers[$block] = new $class(array('document'=>$this->getDocument(), 'element'=>$this));
            }
        }
        return $this->_helpers[$block];
    }

}