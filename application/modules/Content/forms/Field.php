<?php

class Content_Form_Field extends Zend_Form
{
    private $_section;
    public function __construct($section) {
        $this->_section = $section;
        parent::__construct();
    }
    
    public function init()
    {  
     
    //controller will load parent items
        
        $type = $this->createElement('select','element');

        $type->setMultiOptions(array('Text' => 'Text Field',
                                     'Textarea' => 'Text Area',
                                     'Select' => 'Select',
                                     'MultiCheckboxes' => 'CheckBoxes',
                                     'Radio' => 'Radio List'));
        $type->setLabel('Type: ');
        $type->setRequired('true');
        $this->addElement($type);
        
        
        $set = $this->createElement('select','set');
        
        $service = new Content_Model_FieldSet_Service();
        $results = $service->getObjectsBySection($this->_section);
        
        $array = array();
        foreach($results as $set2){
            $array[$set2->id] = $set2->title;
        }

        $set->setMultiOptions($array);
        $set->setLabel('Field Set: ');
        $set->setRequired('true');
        $this->addElement($set);
        
        
        
        $label = $this->createElement('text','label');
        $label->setLabel('Label: ');
        $label->setDescription('This is the label that will show up in the form interface');
        $label->setRequired('true');
        $label->addFilter('StripTags');
        $this->addElement($label);
        
        //use url fiedl type to format nice system name
        $name = $this->createElement('text','name');
        $name->setLabel('System Name: ');
        $name->setDescription('This is the name that will be used in templates');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);
        //@todo check for resevred names
        
        $value = $this->createElement('text','value');
        $value->setLabel('Value: ');
        //$value->setRequired('true');
        $value->addFilter('StripTags');
        $this->addElement($value);
        

        
        
        $instructions = $this->createElement('textarea','description');
        $instructions->setLabel('Instructions: ');
        $instructions->addFilter('StripTags');
        $instructions->setAttrib('rows','3');
        $instructions->setAttrib('cols','20');
        $this->addElement($instructions);
        
      //  $required = $this->createElement('checkbox','required');
      //  $required->setLabel('Required: ');
      //   $required->addMultiOption('1','required');
      //  $this->addElement($required);
        
        $required = $this->createElement('checkbox', 'required',array('uncheckedValue' => ""));
        $required->setLabel('Required: ');
        $this->addElement($required);
        
        
        $order = $this->createElement('text','order');
        $order->setLabel('Order: ');
        $order->setRequired('true');
        $order->addFilter('StripTags');
        $this->addElement($order);


        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Section Field'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    
    
    }


}

