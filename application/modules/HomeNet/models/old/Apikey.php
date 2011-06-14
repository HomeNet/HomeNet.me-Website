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
 * @package HomeNet
 * @subpackage Apikey
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Apikey extends Zend_Db_Table_Row_Abstract
{



    public function generateApikey() {
        return sha1('saltsaddsf'.microtime().$this->house.$this->user);
    }

    public function create(){
       if(empty($this->house)){
           throw new Zend_Exception("House Required");
       }
       if(empty($this->user)){
           $user = new Zend_Session_Namespace('User');
           $this->user = $user->id;
       }

       $key = $this->generateApikey();
       $this->id = $key;
       $this->permissions = '';

       $this->save();

       return $key;
    }

   

}
