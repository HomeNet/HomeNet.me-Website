<?php

class Core_Form_Field extends Zend_Form
{

    public function init()
    {
//        /* F /**
//     * @var int
//     */
//    public $id;
//    /**
//     * @var int
//     */
//    public $section;
//    
//    /**
//     * @var order
//     */
//    public $order;
//    
//    /**
//     * @var string
//     */
//    public $name;
//    /**
//     * @var string
//     */
//    public $name_label;
//    /**
//     * @var string
//     */
//    public $default_value;
//    /**
//     * @var array
//     */
//    public $validators;
//    /**
//     * @var array
//     */
//    public $filters;
//    /**
//     * @var boolean
//     */
//    public $locked = false;
//    
//    public $edit_name = true;
//    
//    public $required = false;
//    
//    public $visible = true;*/
    
     
    //controller will load parent items
        
        $parent = $this->createElement('select','type');

        $type->setMultiOptions(array('text' => 'Text Field',
                                     'textarea' => 'Text Area',
                                     'select' => 'Select',
                                     'checkboxs' => 'CheckBoxes',
                                     'radios' => 'N/A'));
        $parent->setLabel('Type: ');
        $parent->setRequired('true');
        $this->addElement($parent);
        
        
        
        $name_label = $this->createElement('text','name_label');
        $name_label->setLabel('Label: ');
        $name_label->setDescription('This is the label that will show up in the form interface');
        $name_label->setRequired('true');
        $name_label->addFilter('StripTags');
        $this->addElement($name_label);
        
        //use url fiedl type to format nice system name
        $name = $this->createElement('text','name');
        $name->setLabel('System Name: ');
        $name->setDescription('This is the name that will be used in templates');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);
        
        $value = $this->createElement('text','value');
        $value->setLabel('Value: ');
        $value->setRequired('true');
        $value->addFilter('StripTags');
        $value->addElement($name_label);
        

        
        
        $instructions = $this->createElement('textarea','instructions');
        $instructions->setLabel('Instructions: ');
        $instructions->addFilter('StripTags');
        $instructions->setAttrib('rows','3');
        $instructions->setAttrib('cols','20');
        $this->addElement($instructions);
        
        $required = $this->createElement('checkbox','required');
        $required->setLabel('Required: ');
         $required->addMultiOption('1','required');
        $this->addElement($required);
        
        
        $order = $this->createElement('text','Order');
        $order->setLabel('Order: ');
        $order->setRequired('true');
        $order->addFilter('StripTags');
        $this->addElement($order);


        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Section Field'));

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    
    
    }


}

