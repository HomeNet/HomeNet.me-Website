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
abstract class Content_Model_Plugin_Block {

    protected $_value = '';
    protected $_options = array();
    
    const IGNORE_NODE = 0;
    const REMOVE_NODE = 1;

    /**
     * @var DOMNode
     */
    protected $_node;
    
    /**
     * @var DOMDocument
     */
    protected $_document;

    /**
     * @var Zend_View
     */
    protected $_view;
    public $isArray = false;
    public $template;

    public function __construct($config = array()) {
        if (isset($config['value'])) {
            if (($this->isArray == true) && is_string($config['value'])) {
                $config['value'] = unserialize($config['value']);
            }
            $this->_value = $config['value'];
        }
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

        if (isset($config['node'])) {

            if (!($config['node'] instanceof DOMNode)) {
                throw new InvalidArgumentException('Invalid DOM Node Supplied');
            }

            $this->setNode($config['node']);
        }
        if (isset($config['document'])) {

            if (!($config['Document'] instanceof DOMDocument)) {
                throw new InvalidArgumentException('Invalid DOM Node Supplied');
            }

            $this->setDocument($config['document']);
        }
    }

    public function __toString() {
        return $this->renderView();
    }

    public function hasValue() {
        return!empty($this->_value);
    }

    /**
     * Get the value of the element
     * 
     * @param array $values 
     */
    public function getValue() {
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
    }

    public function getView() {
        return $this->_view;
    }

    public function setView($view) {
        $this->_view = $view;
    }

    function _getData($node, $whitelist = null) {
        $data = array();
        if ($node->attributes) {
            if ($whitelist === null) {
                foreach ($node->attributes as $attrName => $attrNode) {
                    if (stristr($attrName, 'data-')) {
                        $data[substr($attrName, 5)] = $attrNode->value;
                    }
                }
            } else {
                foreach ($node->attributes as $attrName => $attrNode) {
                    if (stristr($attrName, 'data-')) {
                        $name = substr($attrName, 5);
                        if (in_array($name, $whitelist)) {
                            $data[$name] = $attrNode->value;
                        }
                    }
                }
            }
        }
        return $data;
    }

    protected function _setData(DOMNode $node, $data, $whitelist = null) {
        if ($whitelist === null) {
            foreach ($data as $key => $value) {
                $node->setIdAttribute($key, $value);
            }
        } else {
            foreach ($data as $key => $value) {
                if (in_array($key, $whitelist)) {
                    $node->setAttribute('data-' . $key, $value);
                }
            }
        }
        return $node;
    }

  
    /**
     * Set Block's DOM Node
     */
    public function setNode($node) {
        $this->_node = $node;
        $this->parseNode();
    }
    
    /**
     * Get Current DOMNode
     */
    public function getNode() {
        return $this->_node;

    }
     /**
     * Set Block's DOM Document
     */
    public function setDocument($document) {
        $this->_document = $document;
    }
    
    /**
     * Get Current DOMDocument
     */
    public function getDocument() {
       return $this->_document;

    }
    
    

    public function parseNode() {
        if (empty($this->_node)) {
            $this->_value = '';
        }

        $this->_value = $this->_getData($this->_node);
    }

    protected function _displayString() {
        
    }

    /*
     * @return DOMNode
     */

    public function getViewNode() {
        
        return self::IGNORE_NODE;
        
//        $doc = new DOMDocument();
//        // $frag = $doc->createDocumentFragment();
//        if (empty($this->template)) {
//            $block = $doc->createElement('div');
//            $this->_setData($block, $this->_value);
//            return $block;
//        }
//
//        $doc->loadHTML(debugArray($this->_value));
//
//        return $doc->getElementsByTagName('body')->item(0)->firstChild;

    }

    public function getFormNode() {
       
        return self::IGNORE_NODE;
//        return $this->renderSave();
    }

    public function getSaveNode() {
        
        return self::IGNORE_NODE;
//
//        $doc = new DOMDocument();
//        $block = $doc->createElement('div');
//        $this->_setData($block, $this->_value);
//        
//
//        return $block;
    }

}