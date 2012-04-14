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
class Content_Plugin_Element_Image_Element extends Content_Model_Plugin_Element {

    public $isArray = true;

    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()) {
        $form = parent::getSetupForm($options);
        $form->setLegend('Image Options');
        $path = $form->createElement('text', 'folder');
        $path->setLabel('Path: ');
        $config = Zend_Registry::get('config');
        $path->setDescription('Path is Prefixed with: ' . $config->site->uploadDirectory . DIRECTORY_SEPARATOR);
        //$path->setRequired('true');
        $path->addFilter('StripTags'); //@todo filter chars
        $path->addFilter('Callback', array('callback' => 'cleanDir'));
        $path->addValidator('Callback', false, array('callback' => array($this, 'validateUploadPath')));
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
        $element = new CMS_Form_Element_JsImage($config);
        $view = Zend_Registry::get('view');
        // $element->setParams($options);
        $element->setParam('url', $view->url(array('controller' => 'content', 'action' => 'rest'), 'content-admin'));

        return $element;
    }

//    public function getSaveValue(){
//        die(debugArray(parent::getSaveValue()));
//   }
}