<?php

namespace SclZfSingleEntityControllerTests;

use SclZfSingleEntityController\Module;

/**
 * Unit tests for {@see Module}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The instance to test.
     *
     * @var Module
     */
    protected $module;

    /**
     * Setup the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->module = new Module();
    }

    /**
     * Test the module bootstrapping code.
     *
     * @covers SclZfSingleEntityController\Module::onBootstrap
     *
     * @return void
     */
    public function testOnBootstrap()
    {
        $event          = $this->getMock('Zend\Mvc\MvcEvent');
        $application    = $this->getMock('Zend\Mvc\ApplicationInterface');
        $serviceManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $eventManager   = $this->getMock('Zend\EventManager\SharedEventManager');
        $listener       = $this->getMock('SclZfSingleEntityController\Listener\ControllerListener');

        $event->expects($this->any())
              ->method('getApplication')
              ->will($this->returnValue($application));

        $application->expects($this->any())
                    ->method('getServiceManager')
                    ->will($this->returnValue($serviceManager));

        $serviceManager->expects($this->at(0))
                      ->method('get')
                      ->with($this->equalTo('SharedEventManager'))
                      ->will($this->returnValue($eventManager));

        $serviceManager->expects($this->at(1))
                       ->method('get')
                       ->with($this->equalTo('SclZfSingleEntityController\Listener\ControllerListener'))
                       ->will($this->returnValue($listener));

        $eventManager->expects($this->once())
                     ->method('attachAggregate')
                     ->with($this->equalTo($listener));

        $this->module->onBootstrap($event);
    }

    /**
     * testGetAutoloaderConfig
     *
     * @covers SclZfSingleEntityController\Module::getAutoloaderConfig
     *
     * @return void
     */
    public function testGetAutoloaderConfig()
    {
        $config = $this->module->getAutoloaderConfig();

        $this->assertArrayHasKey(
            'Zend\Loader\ClassMapAutoloader',
            $config,
            'ClassMapAutoloader config not set.'
        );

        $this->assertFileExists(
            $config['Zend\Loader\ClassMapAutoloader'][0],
            'Class map file doesn\'t exist.'
        );

        $this->assertArrayHasKey(
            'Zend\Loader\StandardAutoloader',
            $config,
            'StandardAutoloader config not set.'
        );
    }

    /**
     * Check the service manager config.
     *
     * @covers SclZfSingleEntityController\Module::getAutoloaderConfig
     *
     * @return void
     */
    public function testGetServiceConfig()
    {
        $config = $this->module->getServiceConfig();

        $this->assertArrayHasKey(
            'invokables',
            $config,
            'Returned result did not have section invokables'
        );

        $this->assertArrayHasKey(
            'SclZfSingleEntityController\Listener\ControllerListener',
            $config['invokables'],
            'Invokables did not have an entry for SclZfSingleEntityController\Listener\ControllerListener'
        );

        $this->assertEquals(
            'SclZfSingleEntityController\Listener\ControllerListener',
            $config['invokables']['SclZfSingleEntityController\Listener\ControllerListener'],
            'Invokables value for SclZfSingleEntityController\Listener\ControllerListener was incorrect'
        );
    }
}
