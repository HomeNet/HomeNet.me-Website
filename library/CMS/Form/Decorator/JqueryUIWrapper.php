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
 * @subpackage Form
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_Form_Decorator_JqueryUIWrapper extends Zend_Form_Decorator_Abstract
{
    /**
     * Default placement: surround content
     * @var string
     */
    protected $_placement = null;

    /**
     * Render
     *
     * Renders as the following:
     * <dt>$dtLabel</dt>
     * <dd>$content</dd>
     *
     * $dtLabel can be set via 'dtLabel' option, defaults to '\&#160;'
     * 
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $elementName = $this->getElement()->getName();
        
        $dtLabel = $this->getOption('dtLabel');
        if( null === $dtLabel ) {
            $dtLabel = '&#160;';
        }

        return '<div class="">' . $dtLabel . $content .'</div>';
    }
}
