<?php

class Content_Form_Section extends CMS_Form
{

    public function init()
    {
 
    
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $this->addElement($title);
        
        //This needs to be a convert from title special field
        $url = $this->createElement('text','url');
        $url->setLabel('Url: ');
        $url->setRequired('true');
        $url->addFilter('StripTags');
        $this->addElement($url);
        
        $description = $this->createElement('textarea','description');
        $description->setLabel('Description: ');
        $description->addFilter('StripTags');
        $description->setAttrib('rows','3');
        $description->setAttrib('cols','20');
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

