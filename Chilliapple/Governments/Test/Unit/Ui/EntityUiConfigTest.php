<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Ui;

use Chilliapple\Governments\Ui\EntityUiConfig;
use PHPUnit\Framework\TestCase;

class EntityUiConfigTest extends TestCase
{
    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testConstruct()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EntityUiConfig('Name\Too\Short');
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testConstructWrongName()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EntityUiConfig('Name\Does\Not\End\With\Proper\Termination');
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getInterface
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetInterface()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface', $uiConfig->getInterface());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getBackLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetBackLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Back', $uiConfig->getBackLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['back' => 'Back to list']]
        );
        $this->assertEquals('Back to list', $uiConfig->getBackLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Save', $uiConfig->getSaveLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['save' => 'Save Entity']]
        );
        $this->assertEquals('Save Entity', $uiConfig->getSaveLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveAndDuplicateLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveAndDuplicateLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Save & Duplicate', $uiConfig->getSaveAndDuplicateLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['save_and_duplicate' => 'Save And clone it']]
        );
        $this->assertEquals('Save And clone it', $uiConfig->getSaveAndDuplicateLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveAndCloseLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveAndCloseLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Save & close', $uiConfig->getSaveAndCloseLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['save_and_close' => 'Save And go to list']]
        );
        $this->assertEquals('Save And go to list', $uiConfig->getSaveAndCloseLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getAllowSaveAndClose
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetAllowSaveAndClose()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertTrue($uiConfig->getAllowSaveAndClose());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['save' => ['allow_close' => false]]
        );
        $this->assertFalse($uiConfig->getAllowSaveAndClose());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getAllowSaveAndDuplicate
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetAllowSaveAndDuplicate()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertTrue($uiConfig->getAllowSaveAndDuplicate());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['save' => ['allow_duplicate' => false]]
        );
        $this->assertFalse($uiConfig->getAllowSaveAndDuplicate());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveFormTarget
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveFormTarget()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $expected = 'somemodule_some_entity_form.somemodule_some_entity_form';
        $this->assertEquals($expected, $uiConfig->getSaveFormTarget());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['save_form_target' => 'save_form_target']
        );
        $this->assertEquals('save_form_target', $uiConfig->getSaveFormTarget());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeleteLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeleteLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Delete', $uiConfig->getDeleteLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['delete' => 'Delete entity']]
        );
        $this->assertEquals('Delete entity', $uiConfig->getDeleteLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeleteMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeleteMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Are you sure you want to delete the item?', $uiConfig->getDeleteMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['delete_message' => 'Really?']]
        );
        $this->assertEquals('Really?', $uiConfig->getDeleteMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeletePopupTitle
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeletePopupTitle()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Delete "${ $.$data.title }"', $uiConfig->getDeletePopupTitle());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['delete_title' => 'Confirm Delete "${ $.$data.%1 }"'], 'name_attribute' => 'name']
        );
        $this->assertEquals('Confirm Delete "${ $.$data.name }"', $uiConfig->getDeletePopupTitle());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getRequestParamName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetRequestParamName()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('some_entity_id', $uiConfig->getRequestParamName());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['request_param' => 'request_param']
        );
        $this->assertEquals('request_param', $uiConfig->getRequestParamName());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getListPageTitle
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetListPageTitle()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Some Entity', $uiConfig->getListPageTitle());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['list' => ['page_title' => 'Page Title']]
        );
        $this->assertEquals('Page Title', $uiConfig->getListPageTitle());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getMenuItem
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetMenuItem()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('SomeNamespace_SomeModule::somemodule_some_entity', $uiConfig->getMenuItem());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['menu' => 'Menu_Item']
        );
        $this->assertEquals('Menu_Item', $uiConfig->getMenuItem());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeleteSuccessMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeleteSuccessMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Item was deleted successfully', $uiConfig->getDeleteSuccessMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['delete' => ['success' => 'Successful delete']]]
        );
        $this->assertEquals('Successful delete', $uiConfig->getDeleteSuccessMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeleteMissingEntityMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeleteMissingEntityMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Item for delete was not found', $uiConfig->getDeleteMissingEntityMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['delete' => ['missing_entity' => 'Missing entity to delete']]]
        );
        $this->assertEquals('Missing entity to delete', $uiConfig->getDeleteMissingEntityMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getGeneralDeleteErrorMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetGeneralDeleteErrorMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('There was a problem deleting the item.', $uiConfig->getGeneralDeleteErrorMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['delete' => ['error' => 'Something Happened']]]
        );
        $this->assertEquals('Something Happened', $uiConfig->getGeneralDeleteErrorMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveSuccessMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveSuccessMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Item was saved successfully.', $uiConfig->getSaveSuccessMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['save' => ['success' => 'Save success']]]
        );
        $this->assertEquals('Save success', $uiConfig->getSaveSuccessMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getSaveErrorMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetSaveErrorMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('There was a problem saving the item.', $uiConfig->getSaveErrorMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['save' => ['error' => 'Save error']]]
        );
        $this->assertEquals('Save error', $uiConfig->getSaveErrorMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDuplicateSuccessMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDuplicateSuccessMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Item was duplicated successfully.', $uiConfig->getDuplicateSuccessMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['save' => ['duplicate' => 'Duplication success']]]
        );
        $this->assertEquals('Duplication success', $uiConfig->getDuplicateSuccessMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getMassDeleteSuccessMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetMassDeleteSuccessMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('5 items were successfully deleted', $uiConfig->getMassDeleteSuccessMessage(5));
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['mass_delete' => ['success' => '%1 items deleted']]]
        );
        $this->assertEquals('5 items deleted', $uiConfig->getMassDeleteSuccessMessage(5));
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getMassDeleteErrorMessage
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetMassDeleteErrorMessage()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('There was a problem deleting the items', $uiConfig->getMassDeleteErrorMessage());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['messages' => ['mass_delete' => ['error' => 'Mass delete error']]]
        );
        $this->assertEquals('Mass delete error', $uiConfig->getMassDeleteErrorMessage());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getNewLabel
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetNewLabel()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('Add new item', $uiConfig->getNewLabel());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['labels' => ['new' => 'Add new']]
        );
        $this->assertEquals('Add new', $uiConfig->getNewLabel());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getNameAttribute
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetNameAttribute()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('title', $uiConfig->getNameAttribute());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['name_attribute' => 'name']
        );
        $this->assertEquals('name', $uiConfig->getNameAttribute());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getPersistoryKey
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetPersistoryKey()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('some_entity', $uiConfig->getPersistoryKey());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['persistor_key' => 'persistor']
        );
        $this->assertEquals('persistor', $uiConfig->getPersistoryKey());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getEditUrlPath
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetEditUrlPath()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('somemodule/someentity/edit', $uiConfig->getEditUrlPath());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['edit_url' => 'edit_url']
        );
        $this->assertEquals('edit_url', $uiConfig->getEditUrlPath());
    }

    /**
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::getDeleteUrlPath
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::parseInterfaceName
     * @covers \Chilliapple\Governments\Ui\EntityUiConfig::__construct
     */
    public function testGetDeleteUrlPath()
    {
        $uiConfig = new EntityUiConfig('SomeNamespace\SomeModule\Api\Data\SomeEntityInterface');
        $this->assertEquals('somemodule/someentity/delete', $uiConfig->getDeleteUrlPath());
        $uiConfig = new EntityUiConfig(
            'SomeNamespace\SomeModule\Api\Data\SomeEntityInterface',
            ['delete_url' => 'delete_url']
        );
        $this->assertEquals('delete_url', $uiConfig->getDeleteUrlPath());
    }
}
