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
abstract class Content_Model_Plugin_BlockHelper {

    /**
     * @var DOMDocument
     */
    protected $_document;
    
      /**
     * @var DOMDocument
     */
    protected $_element;


    /**
     * @var Zend_View
     */
    protected $_view;


    public function __construct($config = array()) {
        if (isset($config['view'])) {

            if (!($config['view'] instanceof Zend_View)) {
                throw new InvalidArgumentException('Invalid Zend View Supplied');
            }

            $this->_view = $config['view'];
        } else {
            $this->_view = Zend_Registry::get('view');
        }
        if (isset($config['document'])) {

            if (!($config['document'] instanceof DOMDocument)) {
                throw new InvalidArgumentException('Invalid DOM Node Supplied');
            }

            $this->setDocument($config['document']);
        }
        if (isset($config['element'])) {

            if (!($config['element'] instanceof Content_Model_Plugin_Element)) {
                throw new InvalidArgumentException('Invalid element Supplied');
            }

            $this->_element = $config['element'];
        }
    }

    public function __toString() {
        return $this->renderView();
    }

    public function getView() {
        return $this->_view;
    }

    public function setView($view) {
        $this->_view = $view;
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


}