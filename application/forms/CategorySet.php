<?php

class Core_Form_CategorySet extends CMS_Form
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

        $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    }


}

