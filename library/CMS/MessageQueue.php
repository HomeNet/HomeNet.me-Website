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
 * @package CMS
 * @subpackage MessageQueue
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_MessageQueue {
/**
 *
 * @var Zend_View
 */
    private $_view;
    
    private $_session;

    const NOTICE  = 'notice';
    const WARNING = 'warning';
    const FATAL   = 'fatal';

    public function  __construct(array $config = array()) {
       $this->_session = new Zend_Session_Namespace('MessageQueue');
       $layout = Zend_Layout::getMvcInstance();
       $this->_view = $layout->getView();
    }

    public function  __toString() {
        return $this->getMessages();
    }

    public function  setView($view) {
        $this->_view = $view;
    }

    public function saveMessage($id, $type, $title, $content, $attribs = array()){

        if(empty($attribs['autodelete'])){
            $attribs['autodelete'] = true;
        }

        $this->_session->$id = array('type' => $type,
                                     'title'=> $title,
                                     'content'=> $content,
                                     'attribs'=> $attribs);
    }

    public function  renderMessages() {
        $messages = array();

        $helper = new CMS_View_Helper_Message();
        $helper->setView($this->_view);

        foreach($this->_session as $key => $value){
            $messages[] = $helper->message($value['type'],$value['title'],$value['content'],$value['sttribs']);
            if($value['sttribs']['autodelete'] == true){
                $this->clearMessage($key);
            }
        }
        return implode("\n",$messages);
    }


    public function  getMessages() {
        $messages = array();

        foreach($this->_session as $value){
            $messages[] = $value;
        }
        return $messages;
    }
    
    public function  getMessage($id) {
        return $this->_session->$id;
    }

    public function  clearMessages() {
        $this->_session->unsetAll();
    }

    public function  clearMessage($id) {
        unset($this->_session->$id);
    }
}