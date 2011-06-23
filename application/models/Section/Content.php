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
 * @subpackage Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Content {
/**
 * id 	int(10) 		UNSIGNED 	No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	revision 	int(10) 		UNSIGNED 	No 		auto_increment 	Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	section 	int(10) 		UNSIGNED 	No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	status 	tinyint(4) 			No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	date 	datetime 			No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	expires 	datetime 			No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	author 	int(10) 		UNSIGNED 	No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	editor 	int(10) 		UNSIGNED 	No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	title 	varchar(255) 	utf8_general_ci 		No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	url 	varchar(255) 	utf8_general_ci 		No 			Browse distinct values 	Change 	Drop 	Primary 	Unique 	Index 	Fulltext
	content
 */
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $revision;
    /**
     * @var int
     */
    public $section;
    /**
     * @var int
     */
    public $status = -1;
    /**
     * @var Zend_Date
     */
    public $created;
    
    /**
     * @var Zend_Date
     */
    public $expires;
    /**
     * @var int
     */
    public $author;
     /**
     * @var int
     */
    public $editor;
    
    /**
     * @var string
     */
    public $url;
    
    /**
     * @var string
     */
    public $title;
    
    
    /**
     * @var array
     */
    public $content;
    
    public $visible;

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
    }

    public function fromArray(array $array) {

        $vars = get_object_vars($this);

        // die(debugArray($vars));

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