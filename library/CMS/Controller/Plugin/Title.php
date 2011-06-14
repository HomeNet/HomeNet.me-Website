<?php

class CMS_Controller_Plugin_Title extends Zend_Controller_Plugin_Abstract {

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

      $inflector = new Zend_Filter_Inflector(':word');
      $inflector->setRules(array(':word'  => array('Word_CamelCaseToSeparator', 'StringToLower')));
      //$inflector->setSeparator(" ");
      $string =  $request->getActionName();
     // $string = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
      $filtered = $inflector->filter(array('word' => $string));

      // pages/camel-cased-words.html


      // pages/this_is_not_camel_cased.html


        $view = Zend_Layout::getMvcInstance()->getView();
        $title = $view->headTitle();
        $view->headTitle()->prepend(ucwords($filtered));
        //$view->headTitle($request->getModuleName());
       // $view->headTitle()->PREPEND//



    }
}