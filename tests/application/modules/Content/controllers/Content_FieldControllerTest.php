<?php

/**
 * Test class for Content_FieldController.
 * Generated by PHPUnit on 2011-11-19 at 15:55:08.
 */
class Content_FieldControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    private $service;
    private $installer;
    private $contentInstaller;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->installer = new Core_Installer();
        $this->installer->installTest();
        $this->installer->loginAsSuperAdmin();
     
        $this->contentInstaller = new Content_Installer();
        $this->contentInstaller->installTest();

        $this->service = new Content_Model_Field_Service();
        
        $this->bootstrap = new Zend_Application('testing', APPLICATION_PATH . '/configs/application.ini'); //
        $this->view = Zend_Registry::get('view');
        
        parent::setUp();
        
        $request = $this->getRequest();
        $request->setModuleName('Content');
        $request->setControllerName('Field');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
     private function createValidObject() {
        $field = new Content_Model_Field();
        $field->section = $this->contentInstaller->section->test->id;
        $field->set = $this->contentInstaller->fieldSet->test->id;
        $field->order = 2;
        $field->element = 'Text';
        $field->name = 'test_name';
        $field->label = 'testLabel';
        $field->value = 'testValue';
        $field->description = 'testDescription';
        $field->required = false;
        $field->visible = true;

        $result = $this->service->create($field);

        $this->assertInstanceOf('Content_Model_Field_Interface', $result);
        return $result;
    }

    public function testIndexAction() {
        //setup
        $object = $this->createValidObject();
        
        $this->getRequest()->setActionName('Index');
        $this->getRequest()->setParam('id', $this->contentInstaller->section->test->id);
        //run
        $this->dispatch();

        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Index');
        $this->assertContains($object->label, $this->response->outputBody());
    }
    

    public function testNewAction_firstView() {
        //setup
        $this->getRequest()->setActionName('New');
        $this->getRequest()->setParam('id', $this->contentInstaller->section->test->id);
        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('New');
        $this->assertNotRedirect();

    }
    
    public function testNewAction_submitInvalid() {
        //setup
        $this->getRequest()->setActionName('New');
        $this->getRequest()->setParam('id', $this->contentInstaller->section->test->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
            'element' => 'Text',
            'name' => '',
            'label' => '',
            'description' => 'testDescription',
            'required' => false,
            'visible' => true));

        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('New');
        $this->assertNotRedirect();
    }
    
    public function testNewAction_submitValid() {
        //setup
        $this->getRequest()->setActionName('New');
        $this->getRequest()->setParam('id', $this->contentInstaller->section->test->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
            'location' => $this->contentInstaller->fieldSet->test->id . '.0',
            'element' => 'Text',
            'name' => 'test_name2',
            'label' => 'testLabel2',
            'description' => 'testDescription',
            'required' => false,
            'visible' => true));

        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('New');
       // $this->fail( $this->response->outputBody());
        $this->assertRedirect("Failed. See Html: \n".$this->response->outputBody());
    }

    public function testEditAction_firstView() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Edit');
        $this->getRequest()->setParam('id', $object->id);

        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Edit');
        $this->assertContains($object->label, $this->response->outputBody()); //make sure data is in the form
        $this->assertNotRedirect();
    }
    
    public function testEditAction_submitInvalid() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Edit');
        $this->getRequest()->setParam('id', $object->id);
         $this->getRequest()->setMethod('POST')
                ->setPost(array(
            'location' => $this->contentInstaller->fieldSet->test->id . '.0',
            'element' => 'Text',
            'name' => '',
            'label' => '',
            'description' => 'testDescription',
            'required' => false,
            'visible' => true));
        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Edit');
        $this->assertNotRedirect();
    }
    
    public function testEditAction_submitValid() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Edit');
        $this->getRequest()->setParam('id', $object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
            'location' => $this->contentInstaller->fieldSet->test->id . '.0',
            'element' => 'Text',
            'name' => 'test_name3',
            'label' => 'testLabel3',
            'description' => 'testDescription erwerwrwer',
            'required' => false,
            'visible' => true));
        
        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Edit');
        $this->assertRedirect("Failed. See Html: \n".$this->response->outputBody());
    }

    public function testDeleteAction_firstView() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Delete');
        $this->getRequest()->setParam('id', $object->id);

        //show form
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Delete');
        $this->assertContains($object->label, $this->response->outputBody()); //make sure data is in the form
        $this->assertNotRedirect();
    }
    public function testDeleteAction_submitCancel() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Delete');
        $this->getRequest()->setParam('id', $object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array('cancel' => 'cancel'));

        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Delete');
        $this->assertRedirect();
    }
    public function testDeleteAction_submitDelete() {
        //setup
        $object = $this->createValidObject();
        $this->getRequest()->setActionName('Delete');
        $this->getRequest()->setParam('id', $object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array('confirm' => 'confirm'));

        //run
        $this->dispatch();
        $this->assertModule('Content');
        $this->assertController('Field');
        $this->assertAction('Delete');
        $this->assertRedirect();
    }

    public function testChangeOrderAjaxAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testElementFormAjaxAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }
}