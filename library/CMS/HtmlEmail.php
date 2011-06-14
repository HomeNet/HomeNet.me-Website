<?php
/* 
 * HtmlEmail.php
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
 * Description of HtmlEmail
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class CMS_HtmlEmail extends Zend_Mail {
    protected $_view;

    protected $_layout;

    public $module = null;

    public function sendHtmlTemplate($template, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {



        $this->_layout->content = $this->_view->render($template);
        $html = $this->_layout->render();

        $this->setBodyHtml($html,$this->getCharset(), $encoding);
        $this->send();
    }

    public function setViewParam($property, $value)
    {
        $this->_view->__set($property, $value);
        return $this;
    }

    public function __construct($module = null, $charset = 'iso-8859-1')
    {
        parent::__construct($charset);

        $this->module = $module;

        //get application config
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();

//die(debugArray($options));

        $this->_layout = new Zend_Layout();
        $this->_view = new Zend_View();

        $defaultTheme = 'default';
        if (isset($options['site']['defaultTheme'])) {
            $defaultTheme = $options['site']['defaultTheme'];
        }

        if ($defaultTheme == 'default') {
            //add default path
            if(!is_null($module) && file_exists(APPLICATION_PATH . '/'.$module. '/layouts/scripts/')){
                $this->_layout->addLayoutPath(APPLICATION_PATH . '/'.$module. '/layouts/scripts/');
            }


            $this->_layout->setLayoutPath(APPLICATION_PATH . '/layouts/scripts/');
            $this->_view->setScriptPath(APPLICATION_PATH . '/views/scripts/email');
        } else {
            if (!file_exists(APPLICATION_PATH . '/themes/' . $defaultTheme)) {
                throw new Zend_Exception('Theme folder Doesn\'t exsist: ' . APPLICATION_PATH . '/themes/' . $defaultTheme);
            }

            if(!is_null($module) && file_exists(APPLICATION_PATH . '/themes/' . $theme .'/'.$module. '/layouts/scripts/')){
                $this->_layout->addLayoutPath(APPLICATION_PATH . '/themes/' . $theme .'/'.$module. '/layouts/scripts/');
            }

            $this->_layout->setLayoutPath(APPLICATION_PATH . '/themes/' . $defaultTheme . '/layouts/scripts/');
            $this->_view->setScriptPath(APPLICATION_PATH . '/themes/' . $defaultTheme. '/views/scripts/email');
        }

        $theme = null;
        if (isset($options['site']['theme'])) {
            $theme = $options['site']['theme'];
        }

        if (!empty($theme)) {
            if (!file_exists(APPLICATION_PATH . '/themes/' . $theme)) {
                throw new Zend_Exception('Theme folder Doesn\'t exsist: ' . APPLICATION_PATH . '/themes/' . $theme);
            }
            if(!is_null($module) && file_exists(APPLICATION_PATH . '/themes/' . $theme .'/'.$module. '/layouts/scripts/')){
                $this->_layout->addLayoutPath(APPLICATION_PATH . '/themes/' . $theme .'/'.$module. '/layouts/scripts/');
            }

            $this->_layout->addLayoutPath(APPLICATION_PATH . '/themes/' . $theme . '/layouts/scripts/');
            $this->_view->addScriptPath(APPLICATION_PATH . '/themes/' . $theme . '/views/scripts/email');
        }
        /*resources.mail.defaultFrom.email = web@homenet.me
resources.mail.defaultFrom.name = "HomeNet"
resources.mail.defaultReplyTo.email = help@homemet.me
resources.mail.defaultReplyTo.name = "HomeNet Help"
         */

       // $this->setFrom($options->resource->defaultFrom->email, $options->resource->defaultFrom->name);

        $this->_layout->setLayout('email');
        $this->_layout->setView($this->_view);

        $this->setViewParam('site',$options['site']);

    }

}