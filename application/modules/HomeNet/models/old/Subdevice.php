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
 * @subpackage Component
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Component extends Zend_Db_Table_Row_Abstract {

    function importArray($array) {
        if (!empty($array['id'])) {
            $this->id = $array['id'];
        }
        $this->device = $array['device'];
        $this->type = $array['type'];
        $this->order = $array['order'];
        $this->name = $array['name'];
    }

    public function add() {
        return $this->save();
    }

    public function update() {
        return $this->save();
    }

    function save() {


        if (parent::save()) {
            return $this->id;
        } else {
            throw new Zend_Exception("Could not save subdevice!");
        }
    }

}

