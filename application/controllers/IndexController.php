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
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('custom');
        
        //  die(debugArray($_SESSION));
//if user is logged in redirect them thier homenet feed
//        $auth = Zend_Auth::getInstance();
//        if($auth->hasIdentity()) {
//            return $this->_forward('Index', 'Index', 'HomeNet');
//        }
        //default shows front page
    }

    public function testAction()
    {
        
    }

    public function plansAction()
    {
        // action body
    }

    public function storeAction()
    {
//die(debugArray($_SESSION['User']['id']));

       $table = new Core_Model_DbTable_Users();
       $user = $table->fetchUserById($_SESSION['User']['id']);
       $user->sendActivationEmail();



die(debugArray('email sent'));
// action body
    }

    public function gettingStartedAction()
    {
        // action body
    }

    public function widgetAction()
    {
        // action body
    }

    public function defaultAction()
    {
        // action body
    }

    

    public function contactAction()
    {
        // action body
    }

    public function comingSoonAction()
    {
        // action body 
    }


}


function fillArray( $depth, $max )
{
    static $seed;
    if ( is_null( $seed ) )
    {
        $seed = array( 'a', 2, 'c', 4, 'e', 6, 'g', 8, 'i', 10 );
    }
    if ( $depth < $max )
    {
        $node = array();
        foreach ( $seed as $key )
        {
                $node[$key] = fillArray( $depth + 1, $max );
        }
        return $node;
    }
    return 'empty';
}

