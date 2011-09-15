<?php

class Content_Form_Template extends Zend_Form
{

    public function init()
    {
//        $title = $this->createElement('text','title');
//        $title->setLabel('Title: ');
//        $title->setRequired('true');
//        $title->addFilter('StripTags');
//        $this->addElement($title);
        
        //This needs to be a convert from title special field
        $url = $this->createElement('text','url');
        $url->setLabel('Url: ');
        $url->setRequired('true');
        $url->addFilter('StripTags');
        $this->addElement($url);
        
        $content = $this->createElement('textarea','content');
        $content->setLabel('Description: ');
        $content->addFilter('StripTags');
        $content->setAttrib('rows','35');
        $content->setAttrib('cols','110');
        $this->addElement($content);


        $this->addDisplayGroup($this->getElements(), 'category', array('legend' => 'Template'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    
    }


}

