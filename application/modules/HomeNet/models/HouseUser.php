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
 * @subpackage HouseUser
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_HouseUser implements HomeNet_Model_HouseUser_Interface {

    public $id = null;
    public $house;
    public $user;
    public $order = 0;
    public $permissions = '';
    
    const PERMISSION_NONE = 0;
    const PERMISSION_VIEW = 1;
    const PERMISSION_EXPORT = 2;
    const PERMISSION_CODE = 3;
    const PERMISSION_ADD = 4;
    const PERMISSION_EDIT = 5;
    const PERMISSION_TRASH = 6;
    const PERMISSION_DELETE = 7;   
    const PERMISSION_CUSTOM = 9;
    const PERMISSION_ADMIN = 10;
    

    

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
    }

    public function fromArray(array $array) {

        $vars = get_object_vars($this);

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $vars)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

}
