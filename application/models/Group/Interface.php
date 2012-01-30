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
 * @subpackage Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
interface Core_Model_Group_Interface extends Zend_Acl_Role_Interface {

    /*
     * code hints
     * 
     * @var public $id int
     * @var public $parent int   
     * @var public $type int
     * @var public $title string
     * @var public $description string
     * @var public $visible bool
     * @var public $user_count int
     * @var public $settings array
     */
    
        /*
     * code hints
     * 
     * @var $id int
     * @var $parent int   
     * @var $type int
     * @var $title string
     * @var $description string
     * @var $visible bool
     * @var $user_count int
     * @var $settings array
     */

    /**
     * @return array
     */
    public function toArray();

    public function fromArray(array $array);
    
    public function setSetting($key, $value);
    
    public function getSetting($key);
    
    public function clearSetting($key);

}
