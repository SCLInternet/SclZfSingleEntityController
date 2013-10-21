<?php

namespace SclZfSingleEntityControllerTests\Listener;

use SclZfSingleEntityController\Listener\ControllerListener;

class ControllerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $controller;

    protected $routeMatch;

    protected function setUp()
    {
        $this->listener = new ControllerListener();

        $this->event = $this->getMock('Zend\Mvc\MvcEvent');

        $this->controller = $this->getMockBuilder('SclZfSingleEntityController\Controller\SingleEntityController')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->routeMatch = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
                                 ->disableOriginalConstructor()
                                 ->getMock();
    }

    /**
     * Test that the event listener is attached.
     *
     * @covers SclZfSingleEntityController\Listener\ControllerListener::attachShared
     *
     * @return void
     */
    public function testAttach() {
        $events = $this->getMock('Zend\EventManager\SharedEventManagerInterface');

        $events->expects($this->once())
               ->method('attach')
               ->with(
                    $this->equalTo('Zend\Mvc\Controller\AbstractActionController'),
                    $this->equalTo(\Zend\Mvc\MvcEvent::EVENT_DISPATCH),
                    $this->equalTo(array($this->listener, 'onDispatch')),
                    $this->equalTo(100)
                );

        $this->listener->attachShared($events);
    }

    /**
     * Test that the event listener is detached.
     *
     * @depends testAttach
     * @covers  SclZfSingleEntityController\Listener\ControllerListener::detachShared
     *
     * @return void
     */
    public function testDetach() {
        $events   = $this->getMock('Zend\EventManager\SharedEventManagerInterface');
        $callback = $this->getMockBuilder('Zend\Stdlib\CallbackHandler')
                         ->disableOriginalConstructor()
                         ->getMock();

        $events->expects($this->once())
               ->method('attach')
               ->will($this->returnValue($callback));

        $this->listener->attachShared($events);

        $events->expects($this->once())
               ->method('detach')
               ->with(
                    $this->equalTo('Zend\Mvc\Controller\AbstractActionController'),
                    $this->equalTo($callback)
                )
               ->will($this->returnValue(true));

        $this->listener->detachShared($events);
    }

    /**
     * Tests that onDispatch exits nicely when called with a non SingleEntityController controller.
     *
     * @covers  SclZfSingleEntityController\Listener\ControllerListener::onDispatch
     *
     * @return void
     */
    public function testOnDispatchWithOtherController()
    {
        $this->event
             ->expects($this->once())
             ->method('getTarget')
             ->will($this->returnValue($this->getMock('Zend\Mvc\Controller\AbstractActionController')));

        $this->event
             ->expects($this->never())
             ->method('getRouteMatch');

        $this->listener->onDispatch($this->event);
    }

    /**
     * Tests that onDispatch exits nicely when an action is called which doesn't require an entity.
     *
     * @covers SclZfSingleEntityController\Listener\ControllerListener::onDispatch
     *
     * @return void
     */
    public function testOnDispatchWithNonRequireAction()
    {
        $requireActions = array('requireAction');
        $action         = 'otherAction';

        $this->event
             ->expects($this->once())
             ->method('getTarget')
             ->will($this->returnValue($this->controller));

        $this->event
             ->expects($this->once())
             ->method('getRouteMatch')
             ->will($this->returnValue($this->routeMatch));

        $this->routeMatch
             ->expects($this->at(0))
             ->method('getParam')
             ->with($this->equalTo('action'))
             ->will($this->returnValue($action));

        $this->controller
             ->expects($this->once())
             ->method('getEntityRequiredActions')
             ->will($this->returnValue($requireActions));

        $this->controller
             ->expects($this->never())
             ->method('setEntity');

        $this->listener->onDispatch($this->event);
    }

    /**
     * Tests that onDispatch throws an error if the mapper is not available.
     *
     * @covers            SclZfSingleEntityController\Listener\ControllerListener::onDispatch
     * @expectedException SclZfSingleEntityController\Exception\NoMapperException
     *
     * @return void
     */
    public function testOnDispatchWithNoMapper()
    {
        $action         = 'requireAction';
        $requireActions = array($action);

        $this->event
             ->expects($this->once())
             ->method('getTarget')
             ->will($this->returnValue($this->controller));

        $this->event
             ->expects($this->once())
             ->method('getRouteMatch')
             ->will($this->returnValue($this->routeMatch));

        $this->routeMatch
             ->expects($this->at(0))
             ->method('getParam')
             ->with($this->equalTo('action'))
             ->will($this->returnValue($action));

        $this->controller
             ->expects($this->once())
             ->method('getEntityRequiredActions')
             ->will($this->returnValue($requireActions));

        $this->controller
             ->expects($this->once())
             ->method('getMapper')
             ->will($this->returnValue(null));

        $this->controller
             ->expects($this->never())
             ->method('setEntity');

        $this->listener->onDispatch($this->event);
    }

    /**
     * Tests that onDispatch throws an error if the mapper is not available.
     *
     * @covers SclZfSingleEntityController\Listener\ControllerListener::onDispatch
     *
     * @return void
     */
    public function testOnDispatchLoadsEntity()
    {
        $action         = 'requireAction';
        $requireActions = array($action);
        $id             = 123;
        $entity         = new \stdClass();

        $mapper = $this->getMock('SclZfGenericMapper\MapperInterface');

        $this->event
             ->expects($this->once())
             ->method('getTarget')
             ->will($this->returnValue($this->controller));

        $this->event
             ->expects($this->once())
             ->method('getRouteMatch')
             ->will($this->returnValue($this->routeMatch));

        $this->routeMatch
             ->expects($this->at(0))
             ->method('getParam')
             ->with($this->equalTo('action'))
             ->will($this->returnValue($action));

        $this->controller
             ->expects($this->once())
             ->method('getEntityRequiredActions')
             ->will($this->returnValue($requireActions));

        $this->controller
             ->expects($this->once())
             ->method('getMapper')
             ->will($this->returnValue($mapper));

        $this->routeMatch
             ->expects($this->at(1))
             ->method('getParam')
             ->with($this->equalTo('id'))
             ->will($this->returnValue($id));

        $mapper->expects($this->once())
               ->method('findById')
               ->with($this->equalTo($id))
               ->will($this->returnValue($entity));

        $this->controller
             ->expects($this->once())
             ->method('setEntity')
             ->with($this->equalTo($entity));

        $this->listener->onDispatch($this->event);
    }
}
