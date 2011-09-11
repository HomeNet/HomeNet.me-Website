<?php

class Content_Form_FieldSet extends CMS_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);
        
        $this->addDisplayGroup($this->getElements(), 'house', array('legend' => 'Category Set'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }


}

