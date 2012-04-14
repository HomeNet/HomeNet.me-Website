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
 * @package Core
 * @subpackage User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Form_Contact extends CMS_Form
{

    public function init()
    {
        $name = $this->createElement('text','name');
        $name->setLabel('Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $email = $this->createElement('text','email');
        $email->setLabel('Email Address: ');
        $email->setRequired('true');
        $email->addFilter('StripTags');
        $email->addValidator('EmailAddress',true);
        $email->addErrorMessage('A valid email address is required!');
        $this->addElement($email);


        $topic = $this->createElement('select', 'topic');
        $topic->setLabel('Topic: ');
        $topic->setRequired('true');

        
        $service = new Content_Model_Section_Service();
        $section = $service->getObjectByUrl('apps');

        $service = new Content_Model_Content_Service();
        $objects = $service->getObjectsBySection($section->id);
        
        
        $options = array('General'=>array(
            'General'=>'General',
            'Website Issues'=>'Website Issues'),
            'Apps' => array()
        );
       // var_dump($objects);
        foreach ($objects as $key => $value) {
            $options['Apps'][(string)$value->url] = (string)$value->title;
        }
        $topic->setMultiOptions($options);
        
        if(!empty($_GET['app']) && isset($options['Apps'][$_GET['app']])){
            $topic->setValue($_GET['app']);
        }
     
        $this->addElement($topic);

        $message = $this->createElement('textarea','message');
        $message->setLabel('Message: ');
        $message->addFilter('StripTags');
        $message->setAttrib('rows','5');
        $message->setAttrib('cols','20');
        $this->addElement($message);

        $group = $this->addDisplayGroup($this->getElements(), 'contact', array ());// 'legend' => 'Account Password'
    }
}

