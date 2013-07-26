<?php

namespace SclZfSingleEntityControllerTests\Controller\SingleEntityController;

use SclZfSingleEntityController\Controller\SingleEntityController;

class SingleEntityControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $controller;

    protected $mapper;

    protected function setUp()
    {
        $this->controller = new SingleEntityController();

        $this->mapper = $this->getMock('SclZfUtilities\Mapper\GenericMapperInterface');
    }

    /**
     * Test the getEntityRequiredActions & setEntityRequiredActions methods.
     *
     * @covers SclZfSingleEntityController\Controller\SingleEntityController::setEntityRequiredActions
     * @covers SclZfSingleEntityController\Controller\SingleEntityController::getEntityRequiredActions
     *
     * @return void
     */
    public function testGetSetEntityRequiredActions()
    {
        $actions = array('view', 'edit');

        $result = $this->controller->setEntityRequiredActions($actions);

        $this->assertSame(
            $this->controller,
            $result,
            'setEntityRequiredActions() did not return $this.'
        );

        $result = $this->controller->getEntityRequiredActions();

        $this->assertEquals(
            $actions,
            $result,
            'getEntityRequiredActions() did not return correct values.'
        );
    }

    /**
     * Test the getMapper & setMapper methods.
     *
     * @covers SclZfSingleEntityController\Controller\SingleEntityController::setMapper
     * @covers SclZfSingleEntityController\Controller\SingleEntityController::getMapper
     *
     * @return void
     */
    public function testGetSetMapper()
    {
        $result = $this->controller->setMapper($this->mapper);

        $this->assertSame(
            $this->controller,
            $result,
            'setMapper() did not return $this.'
        );

        $result = $this->controller->getMapper();

        $this->assertSame(
            $this->mapper,
            $result,
            'getMapper() did not return the mapper.'
        );
    }

    /**
     * When setMapper() is called with an object which is not an instance of GenericMapperInterface
     * an exception should be thrown.
     *
     * @covers            SclZfSingleEntityController\Controller\SingleEntityController::setMapper
     * @expectedException SclZfSingleEntityController\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function testSetMapperWithBadObject()
    {
        $this->controller->setMapper(new \stdClass);
    }

    /**
     * When setEntity() is called but no mapper is set an exception should be thrown.
     *
     * @covers            SclZfSingleEntityController\Controller\SingleEntityController::setMapper
     * @expectedException SclZfSingleEntityController\Exception\NoMapperException
     *
     * @return void
     */
    public function testSetEntityWithNoMapperSet()
    {
        $this->controller->setEntity(new \stdClass());
    }

    /**
     * When setEntity() is with a scalar an exception should be thrown.
     *
     * @covers            SclZfSingleEntityController\Controller\SingleEntityController::setMapper
     * @expectedException SclZfSingleEntityController\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function testSetEntityWithScalar()
    {
        $this->controller->setMapper($this->mapper);
        $this->controller->setEntity('x');
    }

    /**
     * When setEntity() is with a scalar an exception should be thrown.
     *
     * @depends           testGetSetMapper
     * @covers            SclZfSingleEntityController\Controller\SingleEntityController::setMapper
     * @expectedException SclZfSingleEntityController\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function testSetEntityWithIncorrectEntityType()
    {
        $this->controller->setMapper($this->mapper);

        $this->mapper
             ->expects($this->atLeastOnce())
             ->method('getEntityName')
             ->will($this->returnValue('TheEntityClass'));

        $this->controller->setEntity(new \stdClass);
    }

    /**
     * Test the getEntity & setEntity methods.
     *
     * @depends testGetSetMapper
     * @covers  SclZfSingleEntityController\Controller\SingleEntityController::setEntity
     * @covers  SclZfSingleEntityController\Controller\SingleEntityController::getEntity
     *
     * @return void
     */
    public function testSetGetEntity()
    {
        $entity = new \stdClass();

        $this->controller->setMapper($this->mapper);

        $this->mapper
             ->expects($this->atLeastOnce())
             ->method('getEntityName')
             ->will($this->returnValue('stdClass'));

        $result = $this->controller->setEntity($entity);

        $this->assertSame(
            $this->controller,
            $result,
            'setEntity() did not return $this.'
        );

        $result = $this->controller->getEntity();

        $this->assertSame(
            $entity,
            $result,
            'The entity return was not the entity set.'
        );
    }

    /**
     * Test getEntity with no entity set return null.
     *
     * @covers SclZfSingleEntityController\Controller\SingleEntityController::getEntity
     *
     * @return void
     */
    public function testGetEntityWithNoEntityReturnsNull()
    {
        $this->assertNull($this->controller->getEntity());
    }

    /**
     * Test getEntity with $expected set to true and no entity should throw.
     *
     * @covers            SclZfSingleEntityController\Controller\SingleEntityController::getEntity
     * @expectedException SclZfSingleEntityController\Exception\NoEntityException
     *
     * @return void
     */
    public function testGetEntityWithExpectedAndNoEntity()
    {
        $result = $this->controller->getEntity(true);
    }

    /**
     * Test getEntity with $expected set to true.
     *
     * @depends testGetSetMapper
     * @covers  SclZfSingleEntityController\Controller\SingleEntityController::getEntity
     *
     * @return void
     */
    public function testGetEntityWithExpected()
    {
        $entity = new \stdClass();

        $this->controller->setMapper($this->mapper);

        $this->mapper
             ->expects($this->atLeastOnce())
             ->method('getEntityName')
             ->will($this->returnValue('stdClass'));

        $result = $this->controller->setEntity($entity);

        $this->assertSame(
            $this->controller,
            $result,
            'setEntity() did not return $this.'
        );

        $result = $this->controller->getEntity(true);

        $this->assertSame(
            $entity,
            $result,
            'The entity return was not the entity set.'
        );
    }
}
