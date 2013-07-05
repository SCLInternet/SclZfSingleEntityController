<?php

namespace SclZfSingleEntityController\Listener;

use SclZfSingleEntityController\Controller\SingleEntityControllerInterface;
use SclZfSingleEntityController\Exception\NoMapperException;
use SclZfSingleEntityController\Exception\RuntimeException;
use SclZfUtilities\Mapper\GenericMapperInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * Listener for the SingleEntityController system.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ControllerListener implements SharedListenerAggregateInterface
{
    /**
     * The ID of the event manager to attach to.
     */
    const EVENT_MANAGER_ID = 'Zend\Mvc\Controller\AbstractActionController';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * {@inheritDoc}
     *
     * @param  SharedEventManagerInterface $events
     * @return void
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            self::EVENT_MANAGER_ID,
            MvcEvent::EVENT_DISPATCH,
            array($this, 'onDispatch'),
            100
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param  EventManagerInterface $events
     * @return void
     * @todo   Use the trait provided by ZF2
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach(self::EVENT_MANAGER_ID, $callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Event that gets called when a controller is dispatched.
     *
     * Here is where we attempt to load up the entity and inject it into the controller.
     *
     * @param  MvcEvent $event event
     * @return void
     */
    public function onDispatch(MvcEvent $event)
    {
        $controller = $event->getTarget();

        if (!$controller instanceof SingleEntityControllerInterface) {
            return;
        }

        $routeMatch = $event->getRouteMatch();

        if (!in_array($routeMatch->getParam('action'), $controller->getEntityRequiredActions())) {
            return;
        }

        $mapper = $controller->getMapper();

        if (!$mapper instanceof GenericMapperInterface) {
            throw new NoMapperException(__METHOD__);
        }


        $id = $routeMatch->getParam('id');

        if (!$id) {
            throw new RuntimeException('No ID provided in ' . __METHOD__);
        }

        $entity = $mapper->findById($id);

        if (null === $entity) {
            throw new RuntimeException('Failed to load entity');
        }

        $controller->setEntity($entity);
    }
}
