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
 * @subpackage Index
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class ContactController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new Core_Form_Contact();
        $form->addElement('submit', 'submit', array('label' => 'Send'));
     //   $form->addElement('hidden', 'section');

        if (!$this->getRequest()->isPost()) {
            //load exsiting values
//            $values = $object->toArray();
//
//            $e = $form->getElement('location');
//            $e->setValue($values['set'] . '.' . $values['order']);
//
//            $form->populate($values);

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        
        //mail('help@speechzoom.com', $subject, $message);
        
        $values = $form->getValues();
        
        $ip = 'No Ip';
        if(isset($_SERVER["REMOTE_ADDR"])){
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        $browser = 'No User Agent';
        if(isset($_SERVER["HTTP_USER_AGENT"])){
            $browser = $_SERVER["HTTP_USER_AGENT"];
        }
        
        $email = new Zend_Mail();
        $email->setFrom('help@speechzoom.com', 'SpeechZoom Contact Form');
        $email->addTo('help@speechzoom.com','SpeechZoom Help Desk');
        $email->setSubject('Contact Form: '.$values['name'].' - '.$values['topic']);
        $email->setBodyText(
                'Name: '.$values['name']."\n".
                'Email: '.$values['email']."\n".
                'IP: '.$ip."\n".
                'Browser: '.$browser."\n".
                'Topic: '.$values['topic']."\n\n". 
                htmlentities($values['message']));
        $email->setReplyTo($values['email'], $values['name']);
        $email->send();

        $this->view->messages()->add('Thank-you for contacting us. We will try to reply within 48 hours.');
      $form = new Core_Form_Contact();
        $form->addElement('submit', 'submit', array('label' => 'Send'));
          $this->view->form = $form;
       // return $this->_redirect($this->view->url(array('controller' => 'field', 'action' => 'index', 'id' => $object->section), 'content-admin-id'));
    }
}

