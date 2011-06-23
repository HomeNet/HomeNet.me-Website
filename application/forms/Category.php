<?php

class Core_Form_Category extends CMS_Form
{

    public function init()
    {
   
    
    //controller will load parent items
    $parent = $this->createElement('select','parent');
    $parent->addMultiOption('None','');
//        $type->setMultiOptions(array('house' => 'House',
//                                     'apartment' => 'Apartment',
//                                     'condo' => 'Condo',
//                                     'other' => 'Other',
//                                     'na' => 'N/A'));
        $parent->setLabel('Parent: ');
        $parent->setRequired('true');
        $this->addElement($parent);
    
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


        $this->addDisplayGroup($this->getElements(), 'category', array('legend' => 'Category'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    
        
        
    }


}

