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
 * @package Content
 * @subpackage Section
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Form_Section extends CMS_Form {

    public function init() {
        //@todo change this to a form type that can show more details
        $template = $this->createElement('select', 'template');
        $template->setLabel('Template: ');
        $template->setRequired('true');
        //get template options
        $service = new Content_Model_Section_Manager();
        $templates = $service->getTemplates();
        $options = array();
        foreach ($templates as $key => $value) {
            $options[$key] = $value->name;
        }


        //$template->addMultiOption('None','');
        $template->setMultiOptions($options);
        $this->addElement($template);

        $title = $this->createElement('text', 'title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);

        //This needs to be a convert from title special field
        $url = $this->createElement('text', 'url');
        $url->setLabel('Url: ');
        $url->setRequired('true');
        $url->addFilter('StripTags');
        $this->addElement($url);

        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows', '3');
        $description->setAttrib('cols', '20');
        $this->addElement($description);


        //I have no idea why I created this... maybe it's left over from copy/paste
//        $title_label = $this->createElement('text','title_label');
//        $title_label->setLabel('Entry Title Label: ');
//        $title_label->setDescription('This is the label that will show up in the form interface');
//        $title_label->setRequired('true');
//        $title_label->addFilter('StripTags');
//        $this->addElement($title_label);
//  


        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Section'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }

}

