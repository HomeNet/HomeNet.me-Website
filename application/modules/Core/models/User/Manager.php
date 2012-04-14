<?php

/**
 * Description of Manager
 *
 * @author mdoll
 */
class Core_Model_User_Manager {
    
    const ERROR_BANNED = 2;
    const ERROR_NOT_ACTIVATED = 1;
    
    private static $_user = null;
    private $_service;
    
    private $_authClass = 'Core_Model_Auth_Internal';
        
    public function __construct(){
        $this->_service = new Core_Model_User_Service();
    }
    
    /*
     * @return Core_Model_User
     */
    public static function getUser(){
        if(empty(self::$_user)){

            
            if(isset($_SESSION['User'])){
                self::$_user = new Core_Model_User(array('data'=>$_SESSION['User']));
            } else {
                $config = Zend_Registry::get('config');
                $service = new Core_Model_User_Service();
                $guest = $service->getObjectById($config->site->user->guest);
                self::$_user = $guest;
            }
        }
        return self::$_user;
    }
    
    public function setUser(Core_Model_User_Interface $user){
        self::$_user = $user;
    }
    
    public function clearUser(){
        self::$_user = null;
    }
    
    public function setAuthClass($class){
        
        if(!class_exists($class)){
            throw new InvalidArgumentException('Class "'.$class.'" does not exist');      
        }
        
        $this->_authClass = $class;
    }
    
    
    
   public function login(array $credentials) {
       
       //validate that the user isn't already logged in
       
       
     //  if(!is_null($this->_user)){
     //       throw new Exception("User already loaded");
     //   }
       
       /* @var $auth Core_Model_Auth_Interface */
        $auth = new $this->_authClass(); 
       $_SESSION['Core_Auth']['class'] = $this->_authClass;
       try{
        $userId = $auth->login($credentials); //@throws NotFoundException, Exception
       } catch (RequiresFurtherActionException $e){
           $_SESSION['Core_Auth']['credentials'] = $credentials;
           throw new RequiresFurtherActionException();
       }
       
       $user = $this->_service->getObjectById($userId);
       $this->setUser($user);
   
        if($user->status == -1){
             $this->logout();
            throw new CMS_Exception("Account Not Activated", self::ERROR_NOT_ACTIVATED);
        } elseif($user->status < -1){
            $this->logout();
            throw new CMS_Exception("User Banned", self::ERROR_BANNED);
        }
        
        //get memberships
       // $mService = new Core_Model_User_Membership_Service();
        // $this->_user->memberships = $mService->getGroupsByUser($this->_user->id);

        $this->setUser($user);
        $_SESSION['User'] = $user->toArray();
        return $user;
    }

    public function logout() {
//try the same auth adapter used to login with, if not fall back to the default
        //facebook, twitter like to be logged out of too
        if(!empty($_SESSION['Core_Auth']['class'])){
            $auth = new $_SESSION['Core_Auth']['class']();
            $auth->logout();
        } //else {
            $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
        $sessions = Zend_Session::destroy(true);
        $this->clearUser();
       // }
    }

    /**
     * @param Array $values User Info
     * @return boolean
     */
    public function register($values = null) {
        
        if(self::getUser() !== null){
            throw new Zend_Exception("User Already Loaded");
        }   
        
        //set primary_group
        if(empty($values['primary_group'])){
            $config = Zend_Registry::get('config');
            $values['primary_group'] = $config->site->group->default;      
        }
        
        $user = $this->_service->create($values); //throws exception if username exsists

        $auth = new Core_Model_Auth_Internal();
        $auth->add(array('id'=>$user->id, 'username'=>$user->username, 'password'=>$values['password']));
        
        if(!empty($_SESSION['Core_Auth']['credentials'])){
            $auth = new $_SESSION['Core_Auth']['class']();
            $auth->add($_SESSION['Core_Auth']['credentials']);
        }

        $this->setUser($user);
        return $user;
    }
    /**
     * @param string $oldpassword
     * @param string $newpassword
     * @return boolean
     */
    public function changePassword($oldpassword, $newpassword) {

//        if(is_null($this->_user)){
//            throw new Zend_Exception("User Not Loaded");
//        }
//        
//        $auth = new Core_Model_AuthInternal_Service();
//   
//     
//
//        if($this->password !== $this->hashPassword($oldpassword)){
//            throw new CMS_Exception("Old password doesn't match");
//        }
//
//        $this->password = $this->hashPassword($newpassword);
//        $this->save();
    }

    /**
     * @param string $key
     * @throw CMS_Exception
     */
    public function sendActivationEmail() {

        $user = self::getUser();
        
        if (empty($user)) {
            throw new Zend_Exception("User Not Loaded");
        }
        
        

        if ($user->status > 0) {
            // throw new CMS_Exception("User Already Activated");
        }

        $key = uniqid('', true);

        $user->setSetting('activationKey', $key);
        
        $uService = new Core_Model_User_Service();
        $uService->update($user);

        //send email
        $mail = new CMS_HtmlEmail();
        $mail->setSubject('Activate your HomeNet.me Account');
        $mail->addTo($user->email, $user->name);

        $mail->setViewParam('id', $user->id);
        $mail->setViewParam('name', $user->name);
        $mail->setViewParam('email', $user->email);
        $mail->setViewParam('username', $user->username);

        $url = Zend_Layout::getMvcInstance()->getView()->url(array('user' => $user->id, 'action'=>'activate', 'key' => $key), 'core-user');

        $mail->setViewParam('activationUrl', $url);

        $mail->sendHtmlTemplate('activate.phtml');



    }


    /**
     * @param string $key
     * @throw CMS_Exception
     */
    public function activate($key) {

        $user = self::getUser();
        
        if(empty($user)){
            throw new CMS_Exception("User Not Loaded");
        }
        
        

        if($user->status > 0){
            throw new CMS_Exception("User Already Activated");
        }

        $userkey = $user->getSetting('activationKey');

        if($userkey === $key){
            $user->status = 1;
            $uService = new Core_Model_User_Service();
        $uService->update($user);
            return true;
        }

        throw new CMS_Exception("Invalid Activation Key");
    }
}