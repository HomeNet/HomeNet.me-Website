<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Email
 *
 * @author mdoll
 */
class Validate_Email extends Zend_Validate_Abstract
 {
    const INVALID = 'Email is required';
    protected $_messageTemplates = array(
    self::INVALID => "Invalid Email Address",
    self::ALREADYUSED => "Email is already registered"
    );

     public function isValid($value)
     {
         $this->_setValue($value);
         /* if(preg_match($email_regex, trim($value))){
            //$dataModel = new Application_Model_Data(); //check if the email exists
            //if(!$dataModel->email_exists($value)){
            //    return true;
           // }
            else{
                $this->_error(self::ALREADYUSED);
                return false;
            }
          }
          else
          {
            $this->_error(self::INVALID);
            return false;
          }*/
     }

}